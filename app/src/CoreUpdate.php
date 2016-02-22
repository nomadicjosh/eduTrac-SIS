<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * API for Release and Core Updates
 * 
 * @since       6.1.14
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class CoreUpdate
{

    const version = '1.0.0';

    /**
     * URL object.
     * 
     * @var string 
     */
    public $url;

    /**
     * Base API url.
     * 
     * @var string 
     */
    protected $_baseURL = 'etsis.s3.amazonaws.com';
    private $patch_url = '';
    private $local_base_dir = '';
    private $local_backup_dir = '';
    private $default_directory_mode = '0755';
    private $version_filename = 'version.txt';
    private $remove_filename = 'remove-list.txt';

    /**
     * 
     * @var Singleton
     */
    protected static $instance;

    private function __construct()
    {
        // Make sure the script can handle large folders/files for zip and API calls.
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');

        if (function_exists('enable_url_ssl')) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        $this->url = $protocol . $this->_baseURL . '/';
        $this->patch_url = $this->getReleaseJsonUrl();
        $this->local_base_dir = BASE_PATH;
        $this->local_backup_dir = '/tmp/';
    }

    public static function inst()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getReleaseJsonUrl()
    {
        $url = $this->url . 'core/version-check' . '/' . '1.0/release.json';
        return $url;
    }

    public function getServerStatus()
    {
        $status = get_http_response_code($this->getReleaseJsonUrl());

        if ($status != 200) {
            return new \app\src\Core\Exception\Exception(_t('API server is currently unavailable.'), 'api_server');
        }

        return true;
    }

    public function getApiData($v)
    {
        $decode = json_decode($this->getReleaseJsonUrl());
        foreach ($decode->data as $data) {
            return $data->$v;
        }
    }

    public function getApiValue($v)
    {
        $decode = json_decode($this->getReleaseJsonUrl());
        foreach ($decode->data as $data) {
            foreach ($data->values as $value) {
                return $value->$v;
            }
        }
    }

    /**
     * If the zip extention is not loaded, return an exception.
     * 
     * @return \app\src\Core\Exception\Exception
     */
    public function zip_extention_loaded()
    {
        if (!extension_loaded('zip')) {
            return new \app\src\Core\Exception\Exception(_t('The PHP zip extention is not loaded. You must enable the zip extention before continuing.'), 'extention_loaded');
        }
    }

    /**
     * Creates a zipped backup of eduTrac SIS files.
     * 
     * @since 6.1.14
     * @param string $source The base path of the installation
     * @param type $destination Where the zip file is stored.
     */
    public function createBackup($source, $destination)
    {
        if (file_exists($source)) {
            $zip = new \ZipArchive();
            if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
                $source = realpath($source);
                if (is_dir($source)) {
                    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($files as $file) {
                        $file = realpath($file);
                        if (is_dir($file)) {
                            $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                        } else if (is_file($file)) {
                            $zip->addFromString(str_replace($source . '/', '', $file), _file_get_contents($file));
                        }
                    }
                } else if (is_file($source)) {
                    $zip->addFromString(basename($source), _file_get_contents($source));
                }
            }
            return $zip->close();
        }
    }

    public function remoteServerZip($release)
    {
        return $this->url . 'core' . '/' . 'updates' . '/' . $release . '.zip';
    }

    public function localServerZip($release)
    {
        return $this->local_base_dir . 'updates' . '/' . $release . '.zip';
    }

    public function _rmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DS . $object)) {
                        _rmdir($dir . DS . $object);
                    } else {
                        unlink($dir . DS . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public function update()
    {
        $api = _file_get_contents($this->getReleaseJsonUrl());
        $getReleases = json_decode($api);
        $array = [];
        foreach ($getReleases->data as $data) {
            foreach ($data->values as $value) {
                $array[] = $value->release_tag;
            }
        }

        $checkRelease = array_filter($array);
        if (!empty($checkRelease)) {
            echo sprintf('<p>' . _t('Current Release: r%s'), RELEASE_TAG . '</p>');
            echo '<p>' . _t('Reading Current Releases List') . '</p>';
            foreach ($array as $release) {
                if ($release > RELEASE_TAG) {
                    echo sprintf('<p>' . _t('New Update Found: r%s'), $release . '</p>');
                    $found = true;

                    //Download The File If We Do Not Have It
                    if (!is_file($this->localServerZip($release))) {
                        echo '<p>' . _t('Downloading New Update ') . '</p>';
                        $newUpdate = _file_get_contents($this->remoteServerZip($release));
                        //If the updates directory does not exist, create it.
                        if (!is_dir($this->local_base_dir . 'updates' . DS))
                            _mkdir($this->local_base_dir . 'updates' . DS);
                        $dlHandler = fopen($this->localServerZip($release), 'w');
                        //If the update could not be downloaded to the server, then abort the update procedure.
                        if (!fwrite($dlHandler, $newUpdate)) {
                            echo '<p>' . _t('Could not save new update. Operation aborted.') . '</p>';
                            exit();
                        }
                        fclose($dlHandler);
                        echo '<p>' . _t('Update Downloaded and Saved') . '</p>';
                    } else {
                        echo '<p>' . _t('Update already downloaded.') . '</p>';
                    }

                    if ($_GET['coreUpdate'] == true) {
                        //Open The File And Do Stuff
                        $zipHandle = zip_open($this->localServerZip($release));
                        echo '<ul>';
                        while ($newFile = zip_read($zipHandle)) {
                            $thisFileName = zip_entry_name($newFile);
                            $thisFileDir = dirname($thisFileName);

                            //Continue if its not a file
                            if (substr($thisFileName, -1, 1) == '/')
                                continue;

                            //Make the directory if we need to...
                            if (!is_dir($this->local_base_dir . $thisFileDir)) {
                                _mkdir($this->local_base_dir . $thisFileDir);
                                echo '<li>' . sprintf(_t('Created Directory %s'), $thisFileDir) . '</li>';
                            }

                            //Overwrite the file
                            if (!is_dir($this->local_base_dir . $thisFileName)) {
                                echo '<li>' . $thisFileName . '...........';
                                $contents = zip_entry_read($newFile, zip_entry_filesize($newFile));
                                //$contents = str_replace("rn", "n", $contents);
                                $updateThis = '';

                                //If we need to run commands, then do it.

                                $updateThis = fopen($this->local_base_dir . $thisFileName, 'w');
                                fwrite($updateThis, $contents);
                                fclose($updateThis);
                                unset($contents);
                                echo ' <font color="green">' . _t('UPDATED') . '</font></li>';
                            }
                            $this->_rmdir($this->local_base_dir . 'updates');
                        }
                        echo '</ul>';
                        $updated = true;
                    } else {
                        echo '<p>' . _t('Update ready.') . ' <a href="?coreUpdate=true"><b>&raquo; ' . _t('Install Now?') . '</b></a></p>';
                        break;
                    }
                }
            }
            if ($updated == true) {
                echo '<div class="separator bottom"></div><div class="success">&raquo; ' . sprintf(_t('eduTrac SIS updated to r%s'), $release) . '</div>';
            } elseif ($found != true) {
                echo '<p>&raquo; ' . _t('No update is available.') . '</p>';
            }
        } else {
            //echo '<p>' . _t( 'Could not find latest releases.' ) . '</p>';
            return new \app\src\Core\Exception\Exception(_t('Could not find latest releases.'), 'latest_releases');
        }
    }
}

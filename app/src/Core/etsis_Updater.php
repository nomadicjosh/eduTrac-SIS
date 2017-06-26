<?php namespace app\src\Core;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * API for Release and Update Checks
 *
 * @license GPLv3
 *         
 * @since 4.5.3
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Updater
{

    const version = '1.0.2';

    /**
     * Application object
     *
     * @var object
     */
    public $app;

    /**
     * Update object.
     *
     * @var object
     */
    public $update;

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

    /**
     * Holds current installation release
     *
     * @var string
     */
    public $current_release = [];

    /**
     * Holds current installation release value.
     *
     * @var string
     */
    public $current_release_value = [];

    /**
     * URL of json file where array of releases are stored.
     *
     * @var string
     */
    public $patch_url;

    /**
     * The root of the installation.
     *
     * @var string
     */
    public $local_base_dir;

    /**
     * Where backups should be stored.
     *
     * @var string
     */
    public $local_backup_dir;

    /**
     *
     * @var Singleton
     */
    protected static $instance;

    /**
     * Constructor is private so that another instance isn't created.
     *
     * @since 6.2.0
     */
    private function __construct(\Liten\Liten $liten = null)
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
        $this->app = ! empty($liten) ? $liten : \Liten\Liten::getInstance();
        $this->update = new \VisualAppeal\AutoUpdate(rtrim($this->app->config('file.savepath'), '/'), rtrim(BASE_PATH, '/'), 1800);
        $this->current_release = $this->getCurrentRelease();
        $this->current_release_value = $this->current_release['current_release']['current_release_value'];
    }

    public static function inst()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * The url of json file where releases array is stored.
     *
     * @since 6.2.0
     */
    public function getReleaseJsonUrl()
    {
        $url = $this->url . 'core/1.1/update-check/update.json';
        return $url;
    }

    /**
     * Checks the server for online status.
     *
     * @since 6.2.0
     * @return bool|`\app\src\Core\Exception\Exception`
     */
    public function getServerStatus()
    {
        $status = get_http_response_code($this->getReleaseJsonUrl());
        
        if ($status != 200) {
            return new \app\src\Core\Exception\Exception(_t('An unexpected error occurred. Something may be wrong with edutracsis.com or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://www.edutracsis.com/forums/forum/product-support/">support forums</a>.'), 'core_api_failed');
        }
        
        return true;
    }

    protected function getCurrentRelease()
    {
        $file = parse_ini_string(_file_get_contents($this->url . 'core/1.1/update-check/etsis.ini'), true);
        
        return $file;
    }

    /**
     * The url of the release to be downloaded from remote server.
     *
     * @since 6.2.0
     * @param string $release
     *            Release value.
     */
    public function remoteServerZip($release)
    {
        return $this->url . 'core/updates/' . $release . '.zip';
    }

    /**
     * Where the latest release is downloaded on local server.
     *
     * @since 6.2.0
     * @param string $release
     *            Release value.
     */
    public function localServerZip($release)
    {
        return $this->local_base_dir . 'updates/' . $release . '.zip';
    }

    /**
     * Checks if a new release is available.
     * If so, installation along with
     * database will be updated.
     *
     * @since 6.2.2
     */
    public function updateCheck()
    {
        $error = $this->getServerStatus();
        if (is_etsis_exception($error)) {
            echo $error->getMessage();
        } else {
            $this->update->setCurrentVersion(RELEASE_TAG);
            $this->update->setUpdateUrl($this->url . 'core/1.1/update-check');
            
            // Optional:
            $this->update->addLogHandler(new \Monolog\Handler\StreamHandler(APP_PATH . 'tmp' . DS . 'logs' . DS . 'core-update.' . \Jenssegers\Date\Date::now()->format('m-d-Y') . '.txt'));
            $this->update->setCache(new \Desarrolla2\Cache\Adapter\File(APP_PATH . 'tmp/cache'), 3600);
            
            $cacheFile = APP_PATH . 'tmp/cache/__update-versions.php.cache';
            
            echo '<p>' . sprintf(_t('Last checked on %s @ %s'), \Jenssegers\Date\Date::parse(file_mod_time($cacheFile))->format('M d, Y'), \Jenssegers\Date\Date::parse(file_mod_time($cacheFile))->format('h:i A'));
            
            if ($this->update->checkUpdate() !== false) {
                
                if ($this->update->newVersionAvailable()) {
                    // Install new update
                    echo sprintf(_t('<p>New Release: <font color="red">r%s</font></p>'), $this->update->getLatestVersion());
                    echo '<p>' . _t('Installing Updates: ') . '</p>';
                    echo '<pre>';
                    var_dump(array_map(function ($version) {
                        return (string) $version;
                    }, $this->update->getVersionsToUpdate()));
                    echo '</pre>';
                    
                    $result = $this->update->update();
                    
                    echo '<p>' . _t('Database Check . . .') . '</p>';
                    
                    $this->updateDatabaseCheck();
                    
                    echo '<p>' . _t('Directory Check . . .') . '</p>';
                    
                    $this->removeDirCheck();
                    
                    echo '<p>' . _t('File Check . . .') . '</p>';
                    
                    $this->removeFileCheck();
                    
                    if ($result === true) {
                        echo '<p>' . _t('Update successful!') . '</p>';
                    } else {
                        echo '<p>' . sprintf(_t('Update failed: %s!'), $result) . '</p>';
                        
                        if ($result = \VisualAppeal\AutoUpdate::ERROR_SIMULATE) {
                            echo '<pre>';
                            var_dump($this->update->getSimulationResults());
                            echo '</pre>';
                        }
                    }
                } else {
                    echo sprintf(_t('<p>You currently have the latest release of eduTrac SIS installed: <font color="green">r%s</font></p>'), RELEASE_TAG);
                }
            } else {
                echo '<p>' . _t('Could not check for updates! See log file for details.') . '</p>';
            }
        }
    }

    /**
     * Checks to see if database needs to be upgraded.
     *
     * @since 6.2.2
     * @return bool
     */
    public function updateDatabaseCheck()
    {
        $query = [];
        
        if ($this->current_release_value) {
            if (! empty($this->current_release[$this->current_release_value]['query'])) {
                foreach ($this->current_release[$this->current_release_value]['query'] as $query_array) {
                    $query[] = $query_array;
                }
                $this->app->db->beginTransaction();
                foreach ($query as $q) {
                    
                    $sql = $this->app->db->query($q);
                    if ($sql->rowCount() <= 0) {
                        $this->app->db->rollback();
                        return false;
                    }
                }
                $this->app->db->commit();
            }
            return true;
        }
    }

    /**
     * Checks to see if any files need to be removed.
     *
     * @since 6.2.2
     * @return bool
     */
    public function removeFileCheck()
    {
        $file = [];
        
        if ($this->current_release_value) {
            if (! empty($this->current_release[$this->current_release_value]['file'])) {
                foreach ($this->current_release[$this->current_release_value]['file'] as $file_array) {
                    $file[] = $file_array;
                }
                foreach ($file as $f) {
                    
                    $_file = BASE_PATH . $f;
                    
                    if (file_exists($_file)) {
                        unlink($_file);
                    }
                }
            }
            return true;
        }
    }

    /**
     * Checks to see if any directories need to be removed.
     *
     * @since 6.2.2
     * @return bool
     */
    public function removeDirCheck()
    {
        $dir = [];
        
        if ($this->current_release_value) {
            if (! empty($this->current_release[$this->current_release_value]['dir'])) {
                foreach ($this->current_release[$this->current_release_value]['dir'] as $dir_array) {
                    $dir[] = $dir_array;
                }
                foreach ($dir as $d) {
                    
                    $_dir = BASE_PATH . $d;
                    
                    if (is_dir($_dir)) {
                        _rmdir($_dir);
                    }
                }
            }
            return true;
        }
    }
}

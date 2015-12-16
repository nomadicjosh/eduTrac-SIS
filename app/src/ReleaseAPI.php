<?php namespace app\src;

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
class ReleaseAPI
{

    const version = '1.0.1';

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
    protected $_baseURL = 'edutrac.s3.amazonaws.com';

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
     * @since 6.1.15
     */
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

    /**
     * The url of json file where releases array is stored.
     *
     * @since 6.1.15
     */
    public function getReleaseJsonUrl()
    {
        $url = $this->url . 'core/1.1/update-check/update.json';
        return $url;
    }

    /**
     * Checks the server for online status.
     *
     * @since 6.1.15
     * @return bool|\app\src\Exception\Exception
     */
    public function getServerStatus()
    {
        $status = get_http_response_code($this->getReleaseJsonUrl());
        
        if ($status != 200) {
            return new \app\src\Exception\Exception(_t('An unexpected error occurred. Something may be wrong with edutracsis.com or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://www.edutracsis.com/forums/forum/product-support/">support forums</a>.'), 'core_api_failed');
        }
        
        return true;
    }

    /**
     * The url of the release to be downloaded from remote server.
     *
     * @since 6.1.15
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
     * @since 6.1.15
     * @param string $release
     *            Release value.
     */
    public function localServerZip($release)
    {
        return $this->local_base_dir . 'updates/' . $release . '.zip';
    }
}

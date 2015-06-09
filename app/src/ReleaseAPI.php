<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * API for Release and Update Checks
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       4.5.3
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
class ReleaseAPI
{

    protected $_url = 'http://edutrac.s3.amazonaws.com/';
    protected $_json_url;
    protected $_app;

    /**
     * 
     * @var Singleton
     */
    protected static $instance;

    public function __construct(\Liten\Liten $liten = null)
    {
        $this->_json_url = _file_get_contents($this->_url . 'release.json');
        $this->_app = !empty($liten) ? $liten : \Liten\Liten::getInstance();
    }

    public static function inst()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init($value)
    {
        $json = json_decode($this->_json_url, true);
        
        foreach ($json as $v) {
            return $v[$value];
        }
    }

    public function currentRelease()
    {
        return $this->init('CURRENT_RELEASE');
    }

    public function releaseTag()
    {
        return $this->init('RELEASE_TAG');
    }

    public function dbVersion()
    {
        return $this->init('DB_VERSION');
    }

    public function getNotice()
    {
        $notice = $this->_url . $this->init('UPGRADE_NOTICE');
        return $notice;
    }

    public function getSchema()
    {
        $sql = $this->_url . $this->init('UPGRADE_SQL') . DS . $this->_app->hook->{'get_option'}('dbversion') . '.sql';
        return $sql;
    }
}

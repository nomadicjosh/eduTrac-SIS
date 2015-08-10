<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * API for Release and Update Checks
 *  
 * eduTrac SIS
 * Copyright (C) 2013 Joshua Parker
 * 
 * eduTrac SIS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @since       4.5.3
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class ReleaseAPI
{

    protected $_url = 'http://edutrac.s3.amazonaws.com/';
    protected $_json_url;

    /**
     * 
     * @var Singleton
     */
    protected static $instance;

    public function __construct(\Liten\Liten $liten = null)
    {
        $this->_json_url = _file_get_contents($this->_url . 'release.json');
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
        $sql = $this->_url . $this->init('UPGRADE_SQL') . DS . get_option('dbversion') . '.sql';
        return $sql;
    }
}

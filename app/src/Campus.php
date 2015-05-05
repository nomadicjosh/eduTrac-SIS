<?php namespace app\src;
if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Campus Class
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @license     http://www.edutracerp.com/general/edutrac-erp-commercial-license/ Commercial License
 * @link        http://www.7mediaws.org/
 * @since       4.1.4
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */

class Campus {
    
    protected $_app;
	
	public function __construct(\Liten\Liten $liten = NULL) {
        $this->_app = !empty($liten) ? $liten : \Liten\Liten::getInstance();
    }
	
	public static function check($position = '') {
		$subdomain = '';
		$domain_parts = explode('.', $_SERVER['SERVER_NAME']);
		if (count($domain_parts) == 3) {
		    $subdomain = $domain_parts[0];
		
		    if ($subdomain == 'www') {
		        $subdomain = '';
		    }
		}
		if($position == '') {
			return $subdomain;
		} else {
			return $subdomain[$position];
		}
	}
	
	public static function menu() {
		$q = DB::inst()->query( "SHOW TABLES LIKE 'campus'" );
		if($q->rowCount() <= 0 && !hasPermission('create_campus_site')) {
			return ' style="display:none;"';
		}
	}
}
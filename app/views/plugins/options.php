<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Plugin Options View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();

// Handle plugin admin pages
if( isset( $_GET['page'] ) && !empty( $_GET['page'] ) ) {
    $app->hook->{'plugin_admin_page'}( $_GET['page'] );
}

// Handle plugin parent pages
/*if( isset( $_GET['pPage'] ) && !empty( $_GET['pPage'] ) ) {
    Hooks::plugin_parent_page( $_GET['pPage'] );
}*/

// Handle plugin child pages
/*if( isset( $_GET['cPage'] ) && !empty( $_GET['cPage'] ) ) {
    Hooks::plugin_child_page( $_GET['cPage'] );
}*/
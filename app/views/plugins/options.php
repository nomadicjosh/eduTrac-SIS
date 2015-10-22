<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Plugin Options View
 *  
 * @license GPLv3
 * 
 * @since       3.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();

// Handle plugin admin pages
if( isset( $_GET['page'] ) && !empty( $_GET['page'] ) ) {
    $app->hook->plugin_admin_page( $_GET['page'] );
}
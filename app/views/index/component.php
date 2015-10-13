<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Component View
 *  
 * @license GPLv3
 * 
 * @since       6.1.08
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();

// Handle plugin component pages
if( isset( $_GET['cID'] ) && !empty( $_GET['cID'] ) ) {
    $app->hook->{'plugin_component_page'}( $_GET['cID'] );
}
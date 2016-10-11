<?php
/**
 * Step 1: Initial constants defined
 * 
 * Several constants defined in order to help
 * with the autoloader and the loading of other
 * needed functions and files.
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('BASE_PATH') or define('BASE_PATH', __DIR__ . DS);

/**
 * Step 2: Require the Bootstrap
 *
 * The bootstrap includes defines as well as autoloader
 * in order to have a working install of Liten.
 */
require( BASE_PATH . 'Liten' . DS . 'Bootstrap.php');

/**
 * Step 3: Include database config file
 * 
 * This is an example of loaded a database config
 * file when calling an application that needs
 * database connection.
 */
include( BASE_PATH . 'config.php' );
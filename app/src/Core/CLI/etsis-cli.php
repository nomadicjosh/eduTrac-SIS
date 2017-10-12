<?php
header('Access-Control-Allow-Origin: *');

if ( 'cli' !== PHP_SAPI ) {
    echo "This is CLI only.\n";
    die(-1);
}

if ( version_compare( PHP_VERSION, '5.6.0', '<' ) ) {
    printf( "Error: requires PHP %s or newer. You are running version %s.\n", '5.6.0', PHP_VERSION );
    die(-1);
}

date_default_timezone_set('UTC');

// Include database config file.
require_once 'phinx.php';
// Autoload libraries.
require_once 'app/src/vendor/autoload.php';

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 'Off');
ini_set('log_errors', 'On');
ini_set('error_log', 'app/tmp/logs/etsis-cli.' . \Jenssegers\Date\Date::now()->format('m-d-Y') . '.txt');

define( 'ETSIS_CLI_ROOT', __DIR__ );
define( 'ETSIS_CLI_VERSION', '1.1.0' );
// Constant that can be used to check if we are running etsis-cli or not
define( 'ETSIS_CLI', true );

// Include the e-cli classes
require_once ETSIS_CLI_ROOT . '/ETSIS-CLI/etsis_CLI.php';
require_once ETSIS_CLI_ROOT . '/ETSIS-CLI/etsis_CLI_Command.php';
require_once ETSIS_CLI_ROOT . '/php/Mysqldump.php';
require_once ETSIS_CLI_ROOT . '/php/notify.php';

// Load dependencies
ETSIS_CLI::load_dependencies();

require_once ETSIS_CLI_ROOT . '/php/arguments.php';
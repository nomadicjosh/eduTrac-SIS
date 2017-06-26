<?php
/**
 * Config
 *  
 * @license GPLv3
 * 
 * @since       3.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
// Initial Installation Info!
$system = [];
$system['title'] = '{product}';
$system['release'] = '{release}';
$system['installed'] = '{datenow}';

/**
 * If set to PROD, errors will be generated in the logs
 * directory (app/tmp/logs/*.txt). If set to DEV, then
 * errors will be displayed on the screen. For security
 * reasons, when made live to the world, this should be
 * set to PROD.
 */
defined('APP_ENV') or define('APP_ENV', 'PROD');

/**
 * Application path.
 */
defined('APP_PATH') or define('APP_PATH', BASE_PATH . 'app' . DS);

/**
 * Dropins Path.
 */
defined('ETSIS_DROPIN_DIR') or define('ETSIS_DROPIN_DIR', APP_PATH . 'dropins' . DS);

/**
 * Plugins path.
 */
defined('ETSIS_PLUGIN_DIR') or define('ETSIS_PLUGIN_DIR', APP_PATH . 'plugins' . DS);

/**
 * Old Dropins path for backwards compatibility.
 */
defined('DROPINS_DIR') or define('DROPINS_DIR', ETSIS_DROPIN_DIR);

/**
 * Old Plugins path for backwards compatibility.
 */
defined('PLUGINS_DIR') or define('PLUGINS_DIR', ETSIS_PLUGIN_DIR);

/**
 * Cache path.
 */
defined('CACHE_PATH') or define('CACHE_PATH', APP_PATH . 'tmp' . DS . 'cache' . DS);

/**
 * Image path for .pdf's.
 */
defined('K_PATH_IMAGES') or define('K_PATH_IMAGES', BASE_PATH . 'static' . DS . 'images' . DS);

/**
 * Set for low ram cache.
 */
defined('ETSIS_FILE_CACHE_LOW_RAM') or define('ETSIS_FILE_CACHE_LOW_RAM', '');

/**
 * Instantiate a Liten application
 *
 * You can update
 */
$subdomain = '';
$domain_parts = explode('.', $_SERVER['SERVER_NAME']);
if (count($domain_parts) == 3) {
    $subdomain = $domain_parts[0];
} else {
    $subdomain = 'www';
}
$app = new \Liten\Liten(
    [
    'cookies.lifetime' => '86400',
    'cookies.savepath' => ini_get('session.save_path') . DS . $subdomain . DS,
    'file.savepath' => ini_get('session.save_path') . DS . $subdomain . DS . 'files' . DS
    ]
);

/**
 * Database details
 */
defined('DB_HOST') or define('DB_HOST', '{hostname}');
defined('DB_NAME') or define('DB_NAME', '{database}');
defined('DB_USER') or define('DB_USER', '{username}');
defined('DB_PASS') or define('DB_PASS', '{password}');

/**
 * NodeQ noSQL details.
 */
defined('NODEQ_PATH') or define('NODEQ_PATH', $app->config('cookies.savepath') . 'nodes' . DS);
defined('ETSIS_NODEQ_PATH') or define('ETSIS_NODEQ_PATH', NODEQ_PATH . 'etsis' . DS);

/**
 * Do not edit anything from this point on.
 */
$app->inst->singleton('db', function () {
    $pdo = new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'"]);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->query("SET CHARACTER SET 'utf8mb4'");
    return new \Liten\Orm($pdo);
});

/**
 * Require a functions file
 *
 * A functions file may include any dependency injections
 * or preliminary functions for your application.
 */
require( APP_PATH . 'functions.php' );
require( APP_PATH . 'functions' . DS . 'dependency.php' );
require( APP_PATH . 'functions' . DS . 'hook-function.php' );
require( APP_PATH . 'application.php' );

/**
 * Include the routers needed
 *
 * Lazy load the routers. A router is loaded
 * only when it is needed.
 */
include(APP_PATH . 'routers.php');

/**
 * Initialize benchmark.
 */
benchmark_init();

/**
 * Set the timezone for the application.
 */
date_default_timezone_set((get_option('system_timezone') !== NULL) ? get_option('system_timezone') : 'America/New_York');

/**
 * Autoload Dropins
 *
 * Dropins can be plugins and / or routers that
 * should be autoloaded. This is useful when you want to
 * add your own customized screens without needing to touch
 * the core.
 */
$dropins = glob(APP_PATH . 'dropins' . DS . '*.php');
if (is_array($dropins)) {
    foreach ($dropins as $dropin) {
        if (file_exists($dropin))
            include($dropin);
    }
}

/**
 * Run the Liten application
 *
 * This method should be called last. This executes the Liten application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();

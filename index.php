<?php
/**
 * Step 1: Initial constants defined
 * 
 * Several constants defined in order to help
 * with the autoloader and the loading of other
 * needed functions and files.
 */
defined('APP_ENV') or define('APP_ENV', 'PROD');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('BASE_PATH') or define('BASE_PATH', __DIR__ . DS);
defined('APP_PATH') or define('APP_PATH', BASE_PATH . 'app' . DS);
defined('ETSIS_DROPIN_DIR') or define('ETSIS_DROPIN_DIR', BASE_PATH . 'app' . DS . 'dropins' . DS);
defined('ETSIS_PLUGIN_DIR') or define('ETSIS_PLUGIN_DIR', BASE_PATH . 'app' . DS . 'plugins' . DS);
defined('DROPINS_DIR') or define('DROPINS_DIR', ETSIS_DROPIN_DIR);
defined('PLUGINS_DIR') or define('PLUGINS_DIR', ETSIS_PLUGIN_DIR);
defined('CACHE_PATH') or define('CACHE_PATH', APP_PATH . 'tmp/cache' . DS);
defined('K_PATH_IMAGES') or define('K_PATH_IMAGES', BASE_PATH . 'static/images/');

/**
 * Step 1: Require the Bootstrap
 *
 * The bootstrap includes defines as well as autoloader
 * in order to have a working install of Liten.
 */
require( BASE_PATH . 'Liten' . DS . 'Bootstrap.php');

/**
 * Step 2: Instantiate a Liten application
 *
 * This example instantiates a Liten application using
 * its default settings. However, you can configure
 * your Liten application by passing an associative array
 * of setting names and values into the application constructor.
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
    'cookies.savepath' => ini_get('session.save_path') . DS . $subdomain . '/',
    'file.savepath' => ini_get('session.save_path') . DS . $subdomain . '/files/'
    ]
);

/**
 * Step 3: Include database config file
 * 
 * This is an example of loaded a database config
 * file when calling an application that needs
 * database connection.
 */
if (file_exists(BASE_PATH . 'config.php')) {
    include( BASE_PATH . 'config.php' );
}

/**
 * Step 4: Require a functions file
 *
 * A functions file may include any dependency injections
 * or preliminary functions for your application.
 */
require( APP_PATH . 'functions.php' );
require( APP_PATH . 'functions' . DS . 'dependency.php' );
require( APP_PATH . 'functions' . DS . 'global-function.php' );
require( APP_PATH . 'functions' . DS . 'deprecated-function.php' );
require( APP_PATH . 'functions' . DS . 'auth-function.php' );
require( APP_PATH . 'functions' . DS . 'cache-function.php' );
require( APP_PATH . 'functions' . DS . 'textdomain-function.php' );
require( APP_PATH . 'functions' . DS . 'core-function.php' );
require( APP_PATH . 'functions' . DS . 'db-function.php' );
require( APP_PATH . 'functions' . DS . 'course-function.php' );
require( APP_PATH . 'functions' . DS . 'section-function.php' );
require( APP_PATH . 'functions' . DS . 'person-function.php' );
require( APP_PATH . 'functions' . DS . 'student-function.php' );
require( APP_PATH . 'functions' . DS . 'program-function.php' );
require( APP_PATH . 'functions' . DS . 'parsecode-function.php' );
if (file_exists(BASE_PATH . 'config.php')) {
    require( APP_PATH . 'functions' . DS . 'hook-function.php' );
}
require( APP_PATH . 'application.php' );

/**
 * Step 5: Include the routers needed
 *
 * Lazy load the routers. A router is loaded
 * only when it is needed.
 */
include(APP_PATH . 'routers.php');

benchmark_init();
if (file_exists(BASE_PATH . 'config.php')) {
    date_default_timezone_set((get_option('system_timezone') !== NULL) ? get_option('system_timezone') : 'America/New_York');
} else {
    date_default_timezone_set('America/New_York');
}

/**
 * Step 6: Autoload Dropins
 *
 * Dropins can be plugins and / or routers that
 * should be autoloaded. This is useful when you want to
 * add your own customized screens without needing to touch
 * the core.
 */
if (file_exists(BASE_PATH . 'config.php')) {
    $dropins = glob(APP_PATH . 'dropins' . DS . '*.php');
    if (is_array($dropins)) {
        foreach ($dropins as $dropin) {
            if (file_exists($dropin))
                include($dropin);
        }
    }
}

/**
 * Step 7: Run the Liten application
 *
 * This method should be called last. This executes the Liten application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();

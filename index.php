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
$app = new \Liten\Liten(
    [
    'cookies.lifetime' => '86400',
    'cookies.savepath' => '/tmp/' . str_replace('.','_',$app->req->server['SERVER_NAME'] . '/')
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
require( APP_PATH . 'auth-function.php' );
require( APP_PATH . 'core-function.php' );
if (file_exists(BASE_PATH . 'config.php')) {
    require( APP_PATH . 'hook-function.php' );
}
require( APP_PATH . 'application.php' );

/**
 * Step 5: Include the routers needed
 *
 * Here we loop through the routers directory in order
 * to include routes needed at runtime. This helps
 * keep routers organized and the index.php clean.
 */
if (!file_exists(BASE_PATH . 'config.php')) {
    $routers = glob($app->config('routers_dir') . '*.router.php');
    foreach ($routers as $router) {
        if (file_exists($router))
            include($router);
    }
} else {
    /**
     * If you find you have issues with lazy loading
     * the needed routers, then comment out the line below
     * and then uncomment the section above.
     */
    include(APP_PATH . 'routers.php');
}

benchmark_init();
if (file_exists(BASE_PATH . 'config.php')) {
    date_default_timezone_set(($app->hook->{'get_option'}('system_timezone') !== NULL) ? $app->hook->{'get_option'}('system_timezone') : 'America/New_York');
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

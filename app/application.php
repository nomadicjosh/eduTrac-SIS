<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Bootstrap for the application
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

/**
 * Creates a cookies directory with proper permissions.
 */
_mkdir($app->config('cookies.savepath'));

/**
 * Creates a file directory with proper permissions.
 */
_mkdir($app->config('file.savepath'));

/**
 * Creates a cron directory with proper permissions.
 */
_mkdir(cronDir());

/**
 * Creates the cron directory with proper permissions to store
 * cronjob information.
 */
_mkdir(cronDir() . 'cron/logs/');

/**
 * Error log setting
 */
if (APP_ENV == 'DEV') {
    /**
     * Print errors to the screen.
     */
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 'On');
} else {
    /**
     * Log errors to a file.
     */
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'On');
    ini_set('error_log', BASE_PATH . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'error.' . date('m-d-Y') . '.txt');
    set_error_handler('logError', E_ALL & ~E_NOTICE);
}

define('LOCALE_DIR', APP_PATH . 'lang');
$encoding = 'UTF-8';
if (file_exists(BASE_PATH . 'config.php')) {
    $locale = (get_option('et_core_locale') !== null) ? get_option('et_core_locale') : 'en_US';
} else {
    $locale = 'en_US';
}
putenv('LC_MESSAGES='.$locale);

// gettext setup
setlocale(LC_MESSAGES, $locale);
// Set the text domain as 'messages'
$domain = 'eduTrac';
bindtextdomain($domain, LOCALE_DIR);
bind_textdomain_codeset($domain, $encoding);
textdomain($domain);

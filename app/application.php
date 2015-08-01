<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

$app = \Liten\Liten::getInstance();

/**
 * Creates a cron directory with proper permissions.
 */
if (!file_exists(cronDir())) {
    mkdir(cronDir(), 0777, true);
}

/**
 * Creates the cron directory with proper permissions to store
 * cronjob information.
 */
if (!file_exists(cronDir() . 'cron/logs/')) {
    mkdir(cronDir() . 'cron/logs/', 0777, true);
}

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
    $locale = ($app->hook->get_option('et_core_locale') !== null) ? $app->hook->get_option('et_core_locale') : 'en_US';
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

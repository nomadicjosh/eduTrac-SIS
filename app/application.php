<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Bootstrap
 *  
 * eduTrac SIS
 * Copyright (C) 2013 Joshua Parker
 * 
 * eduTrac SIS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

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

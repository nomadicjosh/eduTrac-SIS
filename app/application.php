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
try {
    /**
     * Creates a cookies directory with proper permissions.
     */
    _mkdir($app->config('cookies.savepath'));

    /**
     * Creates a node directory with proper permissions.
     */
    _mkdir($app->config('cookies.savepath') . 'nodes' . DS . 'etsis' . DS);

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
    _mkdir(cronDir() . 'cron' . DS . 'logs' . DS);
} catch (\app\src\Core\Exception\IOException $e) {
    Cascade\Cascade::getLogger('error')->error($e->getMessage());
}

/**
 * Error log setting
 */
etsis_set_environment();

/**
 * Loads the default textdomain.
 * 
 * @since 6.1.09
 */
load_default_textdomain('edutrac-sis', APP_PATH . 'languages' . DS);

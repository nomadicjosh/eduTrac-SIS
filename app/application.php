<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\IOException;
use Cascade\Cascade;

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
} catch (IOException $e) {
    Cascade::getLogger('error')->error(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()));
}

try {
    /**
     * Creates a node directory with proper permissions.
     */
    _mkdir($app->config('cookies.savepath') . 'nodes' . DS . 'etsis' . DS);
} catch (IOException $e) {
    Cascade::getLogger('error')->error(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()));
}

try {
    /**
     * Creates a file directory with proper permissions.
     */
    _mkdir($app->config('file.savepath'));
} catch (IOException $e) {
    Cascade::getLogger('error')->error(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()));
}

try {
    /**
     * Creates the parent backup directory with proper permissions.
     */
    _mkdir($app->config('file.savepath') . 'backups' . DS);
} catch (IOException $e) {
    Cascade::getLogger('error')->error(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()));
}

try {
    /**
     * Creates the database backup directory with proper permissions.
     */
    _mkdir($app->config('file.savepath') . 'backups' . DS . 'database' . DS);
} catch (IOException $e) {
    Cascade::getLogger('error')->error(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()));
}

try {
    /**
     * Creates the system backup directory with proper permissions.
     */
    _mkdir($app->config('file.savepath') . 'backups' . DS . 'system' . DS);
} catch (IOException $e) {
    Cascade::getLogger('error')->error(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()));
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

<?php
if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Global Scope Functions.
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

/**
 * Sets up object cache global scope and assigns it based on
 * the type of caching system used.
 *
 * @since 6.2.0
 */
function _etsis_cache_init()
{
    $app = \Liten\Liten::getInstance();
    
    $driver = $app->hook->apply_filter('etsis_cache_driver', 'file');
    $cache = new \app\src\Core\Cache\etsis_Object_Cache($driver);
    return $cache;
}

/**
 * Sets up custom field global scope.
 *
 * @since 6.2.0
 * @param string $location
 *            Specifies where the custom field will be used.
 */
function _etsis_custom_field($location = 'dashboard')
{
    $field = new \app\src\Core\etsis_CustomField($location);
    return $field;
}

/**
 * Sets up PHPMailer global scope.
 *
 * @since 6.2.0
 * @param bool $bool
 *            Set whether to use exceptions for error handling. Default: true.
 */
function _etsis_phpmailer($bool = true)
{
    $phpMailer = new \PHPMailer($bool);
    return $phpMailer;
}

/**
 * Sets up eduTrac SIS Email global scope.
 *
 * @since 6.2.0
 */
function _etsis_email()
{
    $email = new \app\src\Core\etsis_Email();
    return $email;
}

/**
 * Sets up eduTrac SIS Logger global scope.
 *
 * @since 6.2.11
 */
function _etsis_logger()
{
    $logger = new \app\src\Core\etsis_Logger();
    return $logger;
}
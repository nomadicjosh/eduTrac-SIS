<?php namespace app\src\Core\Exception;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Exception Class
 * 
 * This extends the default `LitenException` class to allow converting
 * exceptions to and from `etsis_Error` objects.
 * 
 * Unfortunately, because an `etsis_Error` object may contain multiple messages and error
 * codes, only the first message for the first error code in the instance will be
 * accessible through the exception's methods.
 *  
 * @since       6.1.14
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class Exception extends \app\src\Core\Exception\BaseException
{
    
}

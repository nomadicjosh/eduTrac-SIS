<?php namespace app\src\Core\Exception;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS File Not Found Exception Class
 * 
 * This extends the default `LitenException` class to allow converting
 * file not found exceptions to and from `etsis_Error` objects.
 * 
 * Unfortunately, because an `etsis_Error` object may contain multiple messages and error
 * codes, only the first message for the first error code in the instance will be
 * accessible through the exception's methods.
 *  
 * @since       6.2.12
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class FileNotFoundException extends \app\src\Core\Exception\BaseException
{
    
}

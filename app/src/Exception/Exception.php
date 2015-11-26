<?php namespace app\src\Exception;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Base Exception Class
 * 
 * This extends the default `LitenException` class to allow converting
 * exceptions to and from `etError` objects.
 * 
 * Unfortunately, because an `etError` object may contain multiple messages and error
 * codes, only the first message for the first error code in the instance will be
 * accessible through the exception's methods.
 *  
 * @since       6.1.14
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class Exception extends \app\src\Exception\BaseException
{

    public function __construct($message = '', $code = '', $previous = null)
    {
        $exception = new \app\src\Exception\BaseException(
            $message, $code
        );
        
        parent::__construct( $message, $code, $previous);
        
        return $exception;
    }
}

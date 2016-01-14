<?php namespace app\src\Core\Exception;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Exception Class
 * 
 * This extends the framework `LitenException` class to allow converting
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
class BaseException extends \Liten\Exception\LitenException {

	/**
	 * eduTrac SIS handles string error codes.
	 * @var string
	 */
	protected $code;

	/**
	 * Error instance.
	 * @var \app\src\Core\etsis_Error
	 */
	protected $etsis_error;

	/**
	 * eduTrac SIS exception constructor.
	 *
	 * The class constructor accepts either the framework `\Liten\Exception\LitenException` creation
	 * parameters or an `\app\src\Core\etsis_Error` instance in place of the previous exception.
	 *
	 * If an `\app\src\Core\etsis_Error` instance is given in this way, the `$message` and `$code`
	 * parameters are ignored in favour of the message and code provided by the
	 * `\app\src\Core\etsis_Error` instance.
	 *
	 * Depending on whether an `\app\src\Core\etsis_Error` instance was received, the instance is kept
	 * or a new one is created from the provided parameters.
	 *
	 * @param string               $message  Exception message (optional, defaults to empty).
	 * @param string               $code     Exception code (optional, defaults to empty).
	 * @param `\Liten\Exception\LitenException` | `\app\src\Core\etsis_Error` $previous Previous exception or error (optional).
	 *
	 * @uses \app\src\Core\etsis_Error
	 * @uses \app\src\Core\etsis_Error::get_error_code()
	 * @uses \app\src\Core\etsis_Error::get_error_message()
	 */
	public function __construct( $message = '', $code = '', $previous = null ) {
		$exception = $previous;
		$etsis_error  = null;

		if ( $previous instanceof \app\src\Core\etsis_Error ) {
			$code      = $previous->get_error_code();
			$message   = $previous->get_error_message( $code );
			$etsis_error  = $previous;
			$exception = null;
		}

		parent::__construct( $message, null, $exception );

		$this->code     = $code;
		$this->etsis_error = $etsis_error;
	}

	/**
	 * Obtain the exception's `\app\src\Core\etsis_Error` object.
	 * 
     * @since 6.1.14
	 * @return etsis_Error eduTrac SIS error.
	 */
	public function get_etsis_error() {
		return $this->etsis_error ? $this->etsis_error : new \app\src\Core\etsis_Error( $this->code, $this->message, $this );
	}

}

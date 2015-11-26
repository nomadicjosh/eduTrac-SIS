<?php namespace app\src\Exception;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Exception Class
 * 
 * This extends the framework `LitenException` class to allow converting
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
class BaseException extends \Liten\Exception\LitenException {

	/**
	 * eduTrac SIS handles string error codes.
	 * @var string
	 */
	protected $code;

	/**
	 * Error instance.
	 * @var \app\src\etError
	 */
	protected $et_error;

	/**
	 * eduTrac SIS exception constructor.
	 *
	 * The class constructor accepts either the framework `\Liten\Exception\LitenException` creation
	 * parameters or an `\app\src\etError` instance in place of the previous exception.
	 *
	 * If an `\app\src\etError` instance is given in this way, the `$message` and `$code`
	 * parameters are ignored in favour of the message and code provided by the
	 * `\app\src\etError` instance.
	 *
	 * Depending on whether an `\app\src\etError` instance was received, the instance is kept
	 * or a new one is created from the provided parameters.
	 *
	 * @param string               $message  Exception message (optional, defaults to empty).
	 * @param string               $code     Exception code (optional, defaults to empty).
	 * @param \Liten\Exception\LitenException | \app\src\etError $previous Previous exception or error (optional).
	 *
	 * @uses \app\src\etError
	 * @uses \app\src\etError::get_error_code()
	 * @uses \app\src\etError::get_error_message()
	 */
	public function __construct( $message = '', $code = '', $previous = null ) {
		$exception = $previous;
		$et_error  = null;

		if ( $previous instanceof \app\src\etError ) {
			$code      = $previous->get_error_code();
			$message   = $previous->get_error_message( $code );
			$et_error  = $previous;
			$exception = null;
		}

		parent::__construct( $message, null, $exception );

		$this->code     = $code;
		$this->et_error = $et_error;
	}

	/**
	 * Obtain the exception's `\app\src\etError` object.
	 * 
     * @since 6.1.14
	 * @return etError eduTrac SIS error.
	 */
	public function get_et_error() {
		return $this->et_error ? $this->et_error : new \app\src\etError( $this->code, $this->message, $this );
	}

}

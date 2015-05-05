<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Install Validator
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @license     http://www.edutracerp.com/general/edutrac-erp-commercial-license/ Commercial License
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
define('STATUS_OK', 'ok');
define('STATUS_WARNING', 'warning');
define('STATUS_ERROR', 'error');

class InstallValidator
{

    public $message;
    public $status;

    public function __construct($message, $status = STATUS_OK)
    {
        $this->message = $message;
        $this->status = $status;
    }
}

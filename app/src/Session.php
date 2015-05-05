<?php namespace app\src;
if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Session Management
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */

class Session {
    
    public function __construct() {}
	
	public static function init()
	{
		if(session_id() == '')
		{
            session_start();
        }
	}
    
	/**
     * sets a specific value to a specific key of the session
     * @param mixed $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
	/**
     * gets/returns the value of a specific key of the session
     * @param mixed $key Usually a string, right ?
     * @return mixed
     */
    public static function get($key)
    {
        if (isset($_SESSION[$key]))
        {
            return $_SESSION[$key];
        }
    }
	
	/**
	 * Sets error message array().
	 */
	public static function error()
	{
		if (self::get('error_message'))
		{
		    foreach (self::get('error_message') as $error)
		    {
		        return '<div class="errormsg">'.$error.'</div>';
		    }
		}
	}
    
	/**
     * Deletes the sessions
     */
    public static function destroy()
    {
        session_unset();
        session_destroy();
    }
    
}
<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Random ID Generator
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
class ID
{

    public static function num($length)
    {
        $characters = "0123456789876543210123456789012345678987654321";
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public static function pass($length)
    {
        $characters = "ad$^SSG@448#%&Fds^@@&#FrRS{F467sS6see}";
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public static function string($length)
    {
        $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZYXWVUTSRQPONMLKJIHGFEDCBAZ";
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    
    public static function code($length)
    {
        $characters = "0123456789876543210ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789YXWVUTSRQPONMLKJIHGFEDCBAZ9876543210";
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}

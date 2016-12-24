<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Deprecated Functions.
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

/**
 * Subject dropdown: shows general list of subjects and
 * if $subjectCode is not NULL, shows the subject attached
 * to a particular record.
 *
 * @deprecated since release 6.1.12
 * @see table_dropdown
 * @since 1.0.0
 * @param string $subjectCode
 *            - optional
 * @return string Returns the record key if selected is true.
 */
function subject_code_dropdown($subjectCode = NULL)
{
    _deprecated_function(__FUNCTION__, '6.1.12', 'table_dropdown');

    $app = \Liten\Liten::getInstance();
    $subj = $app->db->subject()
        ->select('subjectCode,subjectName')
        ->where('subjectCode <> "NULL"');

    $q = $subj->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    foreach ($q as $v) {
        echo '<option value="' . _h($v['subjectCode']) . '"' . selected($subjectCode, _h($v['subjectCode']), false) . '>' . _h($v['subjectCode']) . ' ' . _h($v['subjectName']) . '</option>' . "\n";
    }
}

/**
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout eduTrac to allow for both string or array
 * to be merged into another array.
 *
 * @deprecated since release 6.2.0
 * @since 4.2.0
 * @param string|array $args
 *            Value to merge with $defaults
 * @param array $defaults
 *            Optional. Array that serves as the defaults. Default empty.
 * @return array Merged user defined values with defaults.
 */
function et_parse_args($args, $defaults = '')
{
    _deprecated_function(__FUNCTION__, '6.2.0', 'etsis_parse_args');

    return etsis_parse_args($args, $defaults);
}

/**
 * Hashes a plain text password.
 *
 * @deprecated since release 6.2.0
 * @since 1.0.0
 * @param string $password
 *            Plain text password
 * @return mixed
 */
function et_hash_password($password)
{
    _deprecated_function(__FUNCTION__, '6.2.0', 'etsis_hash_password');

    return etsis_hash_password($password);
}

/**
 * Checks a plain text password against a hashed password.
 *
 * @deprecated since release 6.2.0
 * @since 1.0.0
 * @param string $password
 *            Plain test password.
 * @param string $hash
 *            Hashed password in the database to check against.
 * @param int $person_id
 *            Person ID.
 * @return mixed
 */
function et_check_password($password, $hash, $person_id = '')
{
    _deprecated_function(__FUNCTION__, '6.2.0', 'etsis_check_password');

    return etsis_check_password($password, $hash, $person_id);
}

/**
 * Used by et_check_password in order to rehash
 * an old password that was hashed using MD5 function.
 *
 * @deprecated since release 6.2.0
 * @since 1.0.0
 * @param string $password
 *            Person password.
 * @param int $person_id
 *            Person ID.
 * @return mixed
 */
function et_set_password($password, $person_id)
{
    _deprecated_function(__FUNCTION__, '6.2.0', 'etsis_set_password');

    return etsis_set_password($password, $person_id);
}

/**
 * Parses a string into variables to be stored in an array.
 *
 * Uses {@link http://www.php.net/parse_str parse_str()}
 *
 * @deprecated since release 6.2.0
 * @since 4.2.0
 * @param string $string
 *            The string to be parsed.
 * @param array $array
 *            Variables will be stored in this array.
 */
function et_parse_str($string, $array)
{
    _deprecated_function(__FUNCTION__, '6.2.0', 'etsis_parse_str');

    return etsis_parse_str($string, $array);
}

/**
 *
 * @deprecated since 6.2.0
 * @param unknown $pee            
 * @param number $br            
 */
function et_autop($pee, $br = 1)
{
    _deprecated_function(__FUNCTION__, '6.2.0', 'etsis_autop');

    return etsis_autop($pee, $br);
}

/**
 *
 * @deprecated since release 6.2.0
 * @param string $active            
 */
function et_dropdown_languages($active = '')
{
    _deprecated_function(__FUNCTION__, '6.2.0', 'etsis_dropdown_languages');

    return etsis_dropdown_languages($active);
}

/**
 * 
 * @deprecated since release 6.2.10
 * @return function
 */
function isUserLoggedIn()
{
    _deprecated_function(__FUNCTION__, '6.2.10', 'is_user_logged_in');

    return is_user_logged_in();
}

/**
 * Custom error log function for better PHP logging.
 *
 * @deprecated since release 6.2.11
 * @since 6.2.0
 * @param string $name
 *            Log channel and log file prefix.
 * @param string $message
 *            Message printed to log.
 */
function _error_log($name, $message)
{
    _deprecated_function(__FUNCTION__, '6.2.11', 'etsis_monolog');

    return etsis_monolog($name, $message, $level = 'addInfo');
}

/**
 * Function wrapper for the setError log method.
 * 
 * @deprecated since release 6.2.11
 */
function logError()
{
    _deprecated_function(__FUNCTION__, '6.2.11', 'etsis_error_handler');
}

/**
 * Resizes images.
 * 
 * @deprecated since release 6.3.0
 * @param type $width
 * @param type $height
 * @param type $target
 */
function imgResize($width, $height, $target)
{
    _deprecated_function(__FUNCTION__, '6.3.0', 'resize_image');

    return resize_image($width, $height, $target);
}

/**
 * Makes links in text clickable.
 * 
 * @deprecated since release 6.3.0
 * @param type $text
 */
function clickableLink($text = 'deprecated')
{
    _deprecated_function(__FUNCTION__, '6.3.0', 'make_clickable');
    
    return make_clickable($text);
}

/**
 * Get age by birthdate.
 *
 * @deprecated since release 6.3.0
 * @param string $birthdate
 *            Person's birth date.
 * @return mixed
 */
function getAge($birthdate = '0000-00-00')
{
    _deprecated_function(__FUNCTION__, '6.3.0', 'get_age');
    
    return get_age($birthdate);
}

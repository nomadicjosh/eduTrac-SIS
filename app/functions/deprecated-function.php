<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

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
 *
 * @deprecated since release 6.2.0
 * @see etsis_dropdown_languages
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
 * @see is_user_logged_in
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
 * @see etsis_monolog
 * @since 6.2.0
 * @param string $name
 *            Log channel and log file prefix.
 * @param string $message
 *            Message printed to log.
 */
function _error_log($name, $message)
{
    _deprecated_function(__FUNCTION__, '6.2.11', 'etsis_monolog');

    return etsis_monolog($name, $message);
}

/**
 * Function wrapper for the setError log method.
 * 
 * @deprecated since release 6.2.11
 * @see etsis_error_handler
 */
function logError()
{
    _deprecated_function(__FUNCTION__, '6.2.11', 'etsis_error_handler');
}

/**
 * Resizes images.
 * 
 * @deprecated since release 6.3.0
 * @see resize_image
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
 * @see make_clickable
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
 * @see get_age
 * @param string $birthdate
 *            Person's birth date.
 * @return mixed
 */
function getAge($birthdate = '0000-00-00')
{
    _deprecated_function(__FUNCTION__, '6.3.0', 'get_age');

    return get_age($birthdate);
}

/**
 * When enabled, appends url string in order to give
 * benchmark statistics.
 * 
 * @deprecated since release 6.3.0
 */
function bm()
{
    _deprecated_function(__FUNCTION__, '6.3.0');
}

/**
 * Function for retrieving a person's
 * uploaded school photo.
 *
 * @deprecated since release 6.3.0
 * @see get_school_photo
 * @since 4.5
 * @param int $id
 *            Person ID.
 * @param string $email
 *            Email of the requested person.
 * @param int $s
 *            Size of the photo.
 * @param string $class
 *            HTML element for CSS.
 * @return mixed
 */
function getSchoolPhoto($id, $email, $s = 80, $class = 'thumb')
{
    _deprecated_function(__FUNCTION__, '6.3.0', 'get_school_photo');

    return get_school_photo($id, $email, $s, $class);
}

/**
 * Calculates grade points for stac.
 *
 * @deprecated since release 6.3.0
 * @see calculate_grade_points
 * @param string $grade
 *            Letter grade.
 * @param float $credits
 *            Number of course credits.
 * @return mixed
 */
function acadCredGradePoints($grade, $credits)
{
    _deprecated_function(__FUNCTION__, '6.3.0', 'calculate_grade_points');

    return calculate_grade_points($grade, $credits);
}

/**
 * @deprecated since release 6.3.0
 * @see get_path_info
 * @param string $relative
 * @return string
 */
function getPathInfo($relative)
{
    _deprecated_function(__FUNCTION__, '6.3.0', 'get_path_info');

    return get_path_info($relative);
}

/**
 * A function which returns true if the logged in user
 * is a student in the system.
 *
 * @deprecated since release 6.3.0
 * @see is_student
 * @since 4.3
 * @param int $id
 *            Student's ID.
 * @return bool
 */
function isStudent($id)
{
    _deprecated_function(__FUNCTION__, '6.3.0', 'is_student');

    return is_student($id);
}

/**
 * Acad Level select: shows general list of academic levels and
 * if $levelCode is not NULL, shows the academic level attached
 * to a particular record.
 *
 * @since 1.0.0
 * @deprecated since release 6.3.0
 * @param string $levelCode            
 * @return string Returns the record key if selected is true.
 */
function acad_level_select($levelCode = 'deprecated', $readonly = 'deprecated', $required = 'deprecated')
{
    _deprecated_function(__FUNCTION__, '6.3.0');
}

/**
 * Fee acad Level select: shows general list of academic levels and
 * if $levelCode is not NULL, shows the academic level attached
 * to a particular record.
 *
 * @since 4.1.7
 * @deprecated since release 6.3.0
 * @param string $levelCode            
 * @return string Returns the record key if selected is true.
 */
function fee_acad_level_select($levelCode = 'deprecated')
{
    _deprecated_function(__FUNCTION__, '6.3.0');
}

/**
 * Course Level dropdown: shows general list of course levels and
 * if $levelCode is not NULL, shows the course level attached
 * to a particular record.
 *
 * @since 1.0.0
 * @deprecated since release 6.3.0
 * @param string $levelCode
 * @param string $readonly  
 * @return string Returns the record key if selected is true.
 */
function course_level_select($levelCode = 'deprecated', $readonly = 'deprecated')
{
    _deprecated_function(__FUNCTION__, '6.3.0');
}

/**
 * Class year select: shows general list of class years and
 * if $year is not NULL, shows the class year
 * for a particular student.
 *
 * @deprecated since release 6.3.0
 * @since 1.0.0
 * @param string $year            
 * @return string Returns the record year if selected is true.
 */
function class_year($year = 'deprecated')
{
    _deprecated_function(__FUNCTION__, '6.3.0');
}

/**
 * @deprecated since release 6.3.0
 * @param string $year
 */
function translate_class_year($year = 'deprecated')
{
    _deprecated_function(__FUNCTION__, '6.3.0');
}
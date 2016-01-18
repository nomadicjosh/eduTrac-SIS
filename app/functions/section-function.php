<?php
if (! defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * eduTrac SIS Course Section Functions
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();

/**
 *
 * @since 4.4
 */
function convertCourseSec($sect)
{
    $app = \Liten\Liten::getInstance();
    $section = $app->db->course_sec()
        ->select('courseSecCode')
        ->where('courseSecID = ?', $sect);
    $q = $section->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        $section = $r['courseSecCode'];
    }
    return $section;
}

/**
 * Retrieves course section data given a course section ID or course section array.
 *
 * @since 6.2.0
 * @param int|etsis_Course_Sec|null $section
 *            Course section ID or course section array.
 * @param bool $object
 *            If set to true, data will return as an object, else as an array.
 */
function get_course_sec($section, $object = true)
{
    if ($section instanceof \app\src\Core\etsis_Course_Sec) {
        $_section = $section;
    } elseif (is_array($section)) {
        if (empty($section['courseSecID'])) {
            $_section = new \app\src\Core\etsis_Course_Sec($section);
        } else {
            $_section = \app\src\Core\etsis_Course_Sec::get_instance($section['courseSecID']);
        }
    } else {
        $_section = \app\src\Core\etsis_Course_Sec::get_instance($section);
    }
    
    if (! $_section) {
        return null;
    }
    
    if ($object == true) {
        $_section = array_to_object($_section);
    }
    
    return $_section;
}
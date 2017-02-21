<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;

/**
 * eduTrac SIS Course Functions
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();

/**
 * Prints a list of active courses.
 */
function courseList($id = '')
{
    $app = \Liten\Liten::getInstance();
    try {
        $crse = $app->db->course()
            ->select('courseCode')
            ->where('courseID <> ?', $id)->_and_()
            ->where('currStatus = "A"')->_and_()
            ->where('endDate <= "0000-00-00"');
        $q = $crse->find(function ($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $a = [];
        foreach ($q as $r) {
            $a[] = $r['courseCode'];
        }
        return $a;
    } catch (NotFoundException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (ORMException $e) {
        _etsis_flash()->error($e->getMessage());
    }
}

/**
 * Retrieves course data given a course ID or course array.
 *
 * @since 6.2.0
 * @param int|etsis_Course|null $course
 *            Course ID or course array.
 * @param bool $object
 *            If set to true, data will return as an object, else as an array.
 */
function get_course($course, $object = true)
{
    if ($course instanceof \app\src\Core\etsis_Course) {
        $_course = $course;
    } elseif (is_array($course)) {
        if (empty($course['courseID'])) {
            $_course = new \app\src\Core\etsis_Course($course);
        } else {
            $_course = \app\src\Core\etsis_Course::get_instance($course['courseID']);
        }
    } else {
        $_course = \app\src\Core\etsis_Course::get_instance($course);
    }

    if (!$_course) {
        return null;
    }

    if ($object == true) {
        $_course = array_to_object($_course);
    }

    return $_course;
}

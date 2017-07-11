<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

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
            ->where('endDate IS NULL')->_or_()
            ->whereLte('endDate', '0000-00-00');
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
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
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

/**
 * Checks to see if student meets the prerequisite rule requirements.
 * 
 * @since 6.3.0
 * @param int $stuID Unique student ID.
 * @param int $crseID Unique course ID.
 * @return boolean
 */
function etsis_prereq_rule($stuID, $crseID)
{
    $app = \Liten\Liten::getInstance();

    $crse = get_course($crseID);
    try {
        $rule = _escape($crse->rule);
        if ($rule != null) {
            $prrl = $app->db->query(
                    "SELECT v_sacp.stuID FROM v_sacp"
                    . " INNER JOIN stal ON v_sacp.stuID = stal.stuID AND v_sacp.prog = stal.acadProgCode"
                    . " INNER JOIN v_scrd ON v_sacp.stuID = v_scrd.stuID AND v_sacp.prog = v_scrd.prog"
                    . " WHERE v_sacp.stuID = ?"
                    . " AND $rule", [$stuID]
                )
                ->findOne();

            if (_h($prrl->stuID) <> $stuID) {
                return false;
            }
        }
        return true;
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
}

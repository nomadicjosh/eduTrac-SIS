<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

/**
 * eduTrac SIS Staff Functions
 *
 * @license GPLv3
 *         
 * @since 6.3.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();

/**
 * Faculty dropdown: shows general list of faculty and
 * if $facID is not NULL, shows the faculty attached
 * to a particular record.
 *
 * @since 1.0.0
 * @param string $facID
 *            - optional
 * @return string Returns the record id if selected is true.
 */
function facID_dropdown($facID = NULL)
{
    $app = \Liten\Liten::getInstance();
    try {
        $fac = $app->db->staff_meta()
            ->select('staffID')
            ->where('staffType = "FAC"')
            ->orderBy('staffID')
            ->find();

        foreach ($fac as $v) {
            echo '<option value="' . _h($v->staffID) . '"' . selected($facID, _h($v->staffID), false) . '>' . get_name(_h($v->staffID)) . '</option>' . "\n";
        }
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
 * Retrieve a list af staff members who
 * have active accounts.
 *
 * @since 4.5
 */
function get_staff_email()
{
    $app = \Liten\Liten::getInstance();
    try {
        $email = $app->db->person()
            ->select('person.email,person.personID')
            ->_join('staff', 'person.personID = staff.staffID')
            ->where('staff.status = "A"')
            ->orderBy('person.lname')
            ->find();
        foreach ($email as $v) {
            echo '<option value="' . _h($v->email) . '">' . get_name(_h($v->personID)) . '</option>' . "\n";
        }
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

function supervisor($id, $active = NULL)
{
    $app = \Liten\Liten::getInstance();
    try {
        $q = $app->db->staff()
            ->select('staff.staffID')
            ->whereNot('staff.staffID', $id)
            ->find();

        foreach ($q as $v) {
            echo '<option value="' . _h($v->staffID) . '"' . selected($active, _h($v->staffID), false) . '>' . get_name(_h($v->staffID)) . '</option>' . "\n";
        }
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

function getJobID()
{
    $app = \Liten\Liten::getInstance();
    try {
        $job = $app->db->staff_meta()
            ->select('jobID')
            ->where('staffID = ?', get_persondata('personID'))->_and_()
            ->where('hireDate = (SELECT MAX(hireDate) FROM staff_meta WHERE staffID = ?)', get_persondata('personID'))
            ->findOne();
        return _h($job->jobID);
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

function getJobTitle()
{
    $app = \Liten\Liten::getInstance();
    try {
        $job = $app->db->job()
            ->select('job.title')
            ->_join('staff_meta', 'job.ID = staff_meta.jobID')
            ->where('job.ID = ?', getJobID())
            ->findOne();

        return _h($job->title);
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

function getStaffJobTitle($id)
{
    $app = \Liten\Liten::getInstance();
    try {
        $title = $app->db->job()
            ->select('job.title')
            ->_join('staff_meta', 'job.ID = staff_meta.jobID')
            ->where('staff_meta.staffID = ?', $id)->_and_()
            ->where('staff_meta.hireDate = (SELECT MAX(hireDate) FROM staff_meta WHERE staffID = ?)', $id)
            ->findOne();

        return _h($title->title);
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
 * Retrieves staff data given a staff ID or staff array.
 *
 * @since 6.3.0
 * @param int|etsis_Staff|null $staff
 *            Staff ID or staff array.
 * @param bool $object
 *            If set to true, data will return as an object, else as an array.
 */
function get_staff($staff, $object = true)
{
    if ($staff instanceof \app\src\Core\etsis_Staff) {
        $_staff = $staff;
    } elseif (is_array($staff)) {
        if (empty($staff['staffID'])) {
            $_staff = new \app\src\Core\etsis_Staff($staff);
        } else {
            $_staff = \app\src\Core\etsis_Staff::get_instance($staff['staffID']);
        }
    } else {
        $_staff = \app\src\Core\etsis_Staff::get_instance($staff);
    }

    if (!$_staff) {
        return null;
    }

    if ($object == true) {
        $_staff = array_to_object($_staff);
    }

    return $_staff;
}

/**
 * Retrieves all the tags from every staff
 * and removes duplicates.
 *
 * @since 6.3.0
 * @return mixed
 */
function get_staff_tags()
{
    $app = \Liten\Liten::getInstance();
    try {
        $tagging = $app->db->staff()
            ->select('tags');
        $q = $tagging->find(function ($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $tags = [];
        foreach ($q as $r) {
            $tags = array_merge($tags, explode(",", $r['tags']));
        }
        $tags = array_unique_compact($tags);
        foreach ($tags as $key => $value) {
            if ($value == "" || strlen($value) <= 0) {
                unset($tags[$key]);
            }
        }
        return $tags;
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

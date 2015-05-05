<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac Auth Helper
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac ERP
 * @author      Joshua Parker <josh@7mediaws.org>
 */
function hasPermission($perm)
{
    $acl = new \app\src\ACL();

    if ($acl->hasPermission($perm)) {
        return true;
    } else {
        return false;
    }
}

function get_persondata($field)
{
    $app = \Liten\Liten::getInstance();
    $personID = $app->cookies->getSecureCookie('ET_COOKNAME');
    $value = $app->db->person()
        ->select('person.*,address.*,staff.*,student.*')
        ->_join('address', 'person.personID = address.personID')
        ->_join('staff','person.personID = staff.staffID')
        ->_join('student','person.personID = student.stuID')
        ->where('person.personID = ?', $personID);
    $q = $value->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        return _h($r[$field]);
    }
}

function isUserLoggedIn()
{
    $app = \Liten\Liten::getInstance();

    $person = $app->db->person()->select('personID')
        ->where('person.personID = ?', get_persondata('personID'));
    $q = $person->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    if ($app->cookies->verifySecureCookie('ET_COOKNAME') && count($q) > 0) {
        return true;
    }
    return false;
}

/**
 * Wrapper for the hasPermission() function since
 * this is not really a permission but a restriction.
 * It should give a user/developer more clarity when
 * understanding what this is actually allowing or
 * not allowing a person to do or see.
 * 
 * @since 4.3
 * @param $perm string(required)
 * @return bool
 */
function hasRestriction($perm)
{
    if (hasPermission($perm)) {
        return true;
    } else {
        return false;
    }
}

function ae($perm)
{
    if (!hasPermission($perm)) {
        return ' style="display:none !important;"';
    }
}

function rep($perm)
{
    if (hasRestriction($perm)) {
        return ' readonly="readonly"';
    }
}

/**
 * General Inquiry only on Forms.
 */
function gio()
{
    if (hasRestriction('general_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * General inquiry disable submit buttons.
 */
function gids()
{
    if (hasRestriction('general_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Course Inquiry only.
 */
function cio()
{
    if (hasRestriction('course_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * Course inquiry disable submit buttons.
 */
function cids()
{
    if (hasRestriction('course_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Course Sec Inquiry only.
 */
function csio()
{
    if (hasRestriction('course_sec_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * Course Sec disable submit buttons.
 */
function csids()
{
    if (hasRestriction('course_sec_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Academic Program Inquiry only.
 */
function apio()
{
    if (hasRestriction('acad_prog_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * Academic Program disable submit buttons.
 */
function apids()
{
    if (hasRestriction('acad_prog_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Address Inquiry only.
 */
function aio()
{
    if (hasRestriction('address_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * Address disable submit buttons.
 */
function aids()
{
    if (hasRestriction('address_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Faculty Inquiry only.
 */
function fio()
{
    if (hasRestriction('faculty_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * Faculty disable submit buttons.
 */
function fids()
{
    if (hasRestriction('faculty_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Student Inquiry only.
 */
function sio()
{
    if (hasRestriction('student_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * Student disable submit buttons.
 */
function sids()
{
    if (hasRestriction('student_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Student Account Inquiry only.
 */
function saio()
{
    if (hasRestriction('student_account_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * Student Account disable submit buttons.
 */
function saids()
{
    if (hasRestriction('student_account_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Staff Inquiry only.
 */
function staio()
{
    if (hasRestriction('student_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * Staff disable submit buttons.
 */
function staids()
{
    if (hasRestriction('student_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Person Inquiry only.
 */
function pio()
{
    if (hasRestriction('person_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * Person disable submit buttons.
 */
function pids()
{
    if (hasRestriction('person_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Parent Inquiry only.
 */
function paio()
{
    if (hasRestriction('parent_inquiry_only')) {
        return ' readonly="readonly"';
    }
}

/**
 * Parent disable submit buttons.
 */
function paids()
{
    if (hasRestriction('parent_inquiry_only')) {
        return ' style="display:none !important;"';
    }
}

/**
 * Disable option
 */
function dopt($perm)
{
    if (!hasPermission($perm)) {
        return ' disabled';
    }
}

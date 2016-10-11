<?php
if (! defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * eduTrac SIS Database Related Functions
 *
 * For the most part, these are general purpose functions
 * that use the database to retrieve information.
 *
 * @license GPLv3
 *         
 * @since 6.2.3
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
    $fac = $app->db->staff_meta()
        ->select('staffID')
        ->where('staffType = "FAC"')
        ->orderBy('staffID');
    $q = $fac->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    
    foreach ($q as $v) {
        echo '<option value="' . _h($v['staffID']) . '"' . selected($facID, _h($v['staffID']), false) . '>' . get_name(_h($v['staffID'])) . '</option>' . "\n";
    }
}

/**
 * Table dropdown: pulls dropdown list from specified table
 * if $tableID is not NULL, shows the record attached
 * to a particular record.
 *
 * @since 1.0.0
 * @param string $table
 *            Name of database table that is being queried.
 * @param string $where
 *            Partial where clause (id = '1').
 * @param string $code
 *            Unique code from table.
 * @param string $name
 *            Name or title of record retrieving.
 * @param string $activeID
 *            Field to compare to.
 * @param string $bind
 *            Bind parameters to avoid SQL injection.
 * @return mixed
 */
function table_dropdown($table, $where = null, $id, $code, $name, $activeID = null, $bind = null)
{
    $app = \Liten\Liten::getInstance();
    if ($where !== null && $bind == null) {
        $table = $app->db->query("SELECT $id, $code, $name FROM $table WHERE $where");
    } elseif ($bind !== null) {
        $table = $app->db->query("SELECT $id, $code, $name FROM $table WHERE $where", $bind);
    } else {
        $table = $app->db->query("SELECT $id, $code, $name FROM $table");
    }
    $q = $table->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    
    foreach ($q as $r) {
        echo '<option value="' . _h($r[$code]) . '"' . selected($activeID, _h($r[$code]), false) . '>' . _h($r[$code]) . ' ' . _h($r[$name]) . '</option>' . "\n";
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
    $email = $app->db->person()
        ->select('person.email,person.personID')
        ->_join('staff', 'person.personID = staff.staffID')
        ->where('staff.status = "A"')
        ->orderBy('person.lname');
    $q = $email->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $v) {
        echo '<option value="' . _h($v['email']) . '">' . get_name(_h($v['personID'])) . '</option>' . "\n";
    }
}

/**
 * Date dropdown
 */
function date_dropdown($limit = 0, $name = '', $table = '', $column = '', $id = '', $field = '', $bool = '')
{
    $app = \Liten\Liten::getInstance();
    if ($id != '') {
        $date_select = $app->db->query("SELECT * FROM $table WHERE $column = ?", [
            $id
        ]);
        $q = $date_select->find(function ($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        foreach ($q as $r) {
            $date = explode('-', $r[$field]);
        }
    }
    
    /* years */
    $html_output = '           <select name="' . $name . 'Year"' . $bool . ' class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">' . "\n";
    $html_output .= '               <option value="">&nbsp;</option>' . "\n";
    for ($year = 2000; $year <= (date("Y") - $limit); $year ++) {
        $html_output .= '               <option value="' . sprintf("%04s", $year) . '"' . selected(sprintf("%04s", $year), $date[0], false) . '>' . sprintf("%04s", $year) . '</option>' . "\n";
    }
    $html_output .= '           </select>' . "\n";
    
    /* months */
    $html_output .= '           <select name="' . $name . 'Month"' . $bool . ' class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">' . "\n";
    $html_output .= '               <option value="">&nbsp;</option>' . "\n";
    $months = array(
        "",
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
    );
    for ($month = 1; $month <= 12; $month ++) {
        $html_output .= '               <option value="' . sprintf("%02s", $month) . '"' . selected(sprintf("%02s", $month), $date[1], false) . '>' . $months[$month] . '</option>' . "\n";
    }
    $html_output .= '           </select>' . "\n";
    
    /* days */
    $html_output .= '           <select name="' . $name . 'Day"' . $bool . ' class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">' . "\n";
    $html_output .= '               <option value="">&nbsp;</option>' . "\n";
    for ($day = 1; $day <= 31; $day ++) {
        $html_output .= '               <option value="' . sprintf("%02s", $day) . '"' . selected(sprintf("%02s", $day), $date[2], false) . '>' . sprintf("%02s", $day) . '</option>' . "\n";
    }
    $html_output .= '           </select>' . "\n";
    
    return $html_output;
}

function supervisor($id, $active = NULL)
{
    $app = \Liten\Liten::getInstance();
    
    $s = $app->db->staff()
        ->select('staff.staffID')
        ->whereNot('staff.staffID', $id);
    
    $q = $s->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    
    foreach ($q as $v) {
        echo '<option value="' . _h($v['staffID']) . '"' . selected($active, _h($v['staffID']), false) . '>' . get_name(_h($v['staffID'])) . '</option>' . "\n";
    }
}

function getJobID()
{
    $app = \Liten\Liten::getInstance();
    $job = $app->db->staff_meta()
        ->select('jobID')
        ->where('staffID = ?', get_persondata('personID'))->_and_()
        ->where('hireDate = (SELECT MAX(hireDate) FROM staff_meta WHERE staffID = ?)', get_persondata('personID'))
        ->findOne();
    return _h($job->jobID);
}

function getJobTitle()
{
    $app = \Liten\Liten::getInstance();
    $job = $app->db->job()
        ->select('job.title')
        ->_join('staff_meta', 'job.ID = staff_meta.jobID')
        ->where('job.ID = ?', getJobID())
        ->findOne();
    
    return _h($job->title);
}

function getStaffJobTitle($id)
{
    $app = \Liten\Liten::getInstance();
    $title = $app->db->job()
        ->select('job.title')
        ->_join('staff_meta', 'job.ID = staff_meta.jobID')
        ->where('staff_meta.staffID = ?', $id)->_and_()
        ->where('staff_meta.hireDate = (SELECT MAX(hireDate) FROM staff_meta WHERE staffID = ?)', $id)
        ->findOne();
    
    return _h($title->title);
}

/**
 *
 * @since 4.5
 */
function is_count_zero($table, $field, $ID)
{
    $app = \Liten\Liten::getInstance();
    $zero = $app->db->query("SELECT $field FROM $table WHERE $field = ?", [
        $ID
    ]);
    $q = $zero->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    if (count($q) > 0) {
        return 'X';
    }
}

/**
 * Returns the ID of the person if he/she has an application.
 *
 * @param int $id
 *            Person ID
 * @return int
 */
function hasAppl($id)
{
    $app = \Liten\Liten::getInstance();
    
    $appl = $app->db->application()
        ->where('personID = ?', $id)
        ->findOne();
    
    return _h($appl->personID);
}

/**
 * Custom function to query any eduTrac SIS
 * database table.
 *
 * @since 6.0.00
 * @param string $table            
 * @param mixed $field            
 * @param mixed $where            
 * @return mixed
 */
function qt($table, $field, $where = null)
{
    $app = \Liten\Liten::getInstance();
    if ($where !== null) {
        $query = $app->db->query("SELECT * FROM $table WHERE $where");
    } else {
        $query = $app->db->query("SELECT * FROM $table");
    }
    $result = $query->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($result as $r) {
        return _h($r[$field]);
    }
}


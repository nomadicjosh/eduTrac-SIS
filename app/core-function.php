<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * eduTrac Core Functions
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
define('CURRENT_RELEASE', '5.0');
define('RELEASE_TAG', '5.0.1');

$app = \Liten\Liten::getInstance();

function _t($msgid) {
    return gettext($msgid);
}

function getPathInfo($relative)
{
    $app = \Liten\Liten::getInstance();
    $base = basename(BASE_PATH);
    if (strpos($app->req->server['REQUEST_URI'], DS . $base . $relative) === 0) {
        return $relative;
    } else {
        return $app->req->server['REQUEST_URI'];
    }
}

/**
 * Custom function to use curl or use file_get_contents
 * if curl is not available.
 */
function _file_get_contents($url) {
    if (!function_exists('curl_init')){ 
        return file_get_contents($url);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * Bookmarking initialization function.
 * 
 * @since 1.1.3
 */
function benchmark_init()
{
    if (isset($_GET['php-benchmark-test'])) {
        \app\src\PHPBenchmark\Monitor::instance()->init(!empty($_GET['display-data']));
        \app\src\PHPBenchmark\Monitor::instance()->snapshot('Bootstrap finished');
    }
}
if (!function_exists('imgResize')) {

    function imgResize($width, $height, $target)
    {
        //takes the larger size of the width and height and applies the formula. Your function is designed to work with any image in any size.
        if ($width > $height) {
            $percentage = ($target / $width);
        } else {
            $percentage = ($target / $height);
        }

        //gets the new value and applies the percentage, then rounds the value
        $width = round($width * $percentage);
        $height = round($height * $percentage);
        //returns the new sizes in html image tag format...this is so you can plug this function inside an image tag so that it will set the image to the correct size, without putting a whole script into the tag.
        return "width=\"$width\" height=\"$height\"";
    }
}

// An alternative function of using the echo command.
if (!function_exists('_e')) {

    function _e($string)
    {
        echo $string;
    }
}

if (!function_exists('clickableLink')) {

    function clickableLink($text = '')
    {
        $text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);
        $ret = ' ' . $text;
        $ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);

        $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
        $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
        $ret = substr($ret, 1);
        return $ret;
    }
}

/**
 * Renders any unwarranted special characters to HTML entities.
 * 
 * @since 5.0
 * @param string $str
 * @return mixed
 */
function _escape($str, $disabledEvents = null)
{
    $allowedTags = array('<a>', '<b>', '<blockquote>', '<br>', '<cite>', '<code>', '<del>', '<div>', '<em>', '<ul>', '<ol>', '<li>', '<dl>', '<dt>', '<dd>', '<img>', '<ins>', '<u>', '<q>', '<h3>', '<h4>', '<h5>', '<h6>', '<samp>', '<strong>', '<sub>', '<sup>', '<p>', '<table>', '<tr>', '<td>', '<th>', '<pre>', '<span>');
    $disabledEvents = array('onclick', 'ondblclick', 'onkeydown', 'onkeypress', 'onkeyup', 'onload', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onunload');
    {
        if (empty($disabledEvents)) {
            return strip_tags($str, implode('', $allowedTags));
        }
        return preg_replace('/<(.*?)>/ies', "'<' . preg_replace(array('/javascript:[^\"\']*/i', '/(" . implode('|', $disabledEvents) . ")=[\"\'][^\"\']*[\"\']/i', '/\s+/'), array('', '', ' '), stripslashes('\\1')) . '>'", strip_tags($str, implode('', $allowedTags)));
    }
}

/**
 * Hide menu links by functions and/or by 
 * permissions.
 * 
 * @since 4.0.4
 */
function hl($f, $p = NULL)
{
    if (function_exists($f)) {
        return ' style="display:none !important;"';
    }
    if ($p !== NULL) {
        return ae($p);
    }
}

/**
 * Function used to check the installation
 * of a particular module. If module exists, 
 * unhide it's links throughout the system.
 */
function ml($func)
{
    if (!function_exists($func)) {
        return ' style="display:none !important;"';
    }
}

/**
 * When enabled, appends url string in order to give
 * benchmark statistics.
 * 
 * @since 1.0.0
 */
function bm()
{
    $app = \Liten\Liten::getInstance();
    if ($app->hook->get_option('enable_benchmark') == 1) {
        return '?php-benchmark-test=1&display-data=1';
    }
}

function _bool($num)
{
    switch ($num) {
        case 1:
            return 'Yes';
            break;
        case 0:
            return 'No';
            break;
    }
}

function courseList($id)
{
    $app = \Liten\Liten::getInstance();
    $crse = $app->db->course()
        ->select('courseCode')
        ->where('courseID <> ?', $id)->_and_()
        ->where('currStatus = "A"')->_and_()
        ->where('endDate <= "0000-00-00"');
    $q = $crse->find(function($data) {
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
}

/**
 * Subject dropdown: shows general list of subjects and
 * if $subjectCode is not NULL, shows the subject attached 
 * to a particular record.
 * 
 * @since 1.0.0
 * @param string $subjectID - optional
 * @return string Returns the record key if selected is true.
 */
function subject_code_dropdown($subjectCode = NULL)
{
    $app = \Liten\Liten::getInstance();
    $subj = $app->db->query('SELECT subjectCode,subjectName FROM subject WHERE subjectCode <> "NULL"');

    $q = $subj->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    foreach ($q as $k => $v) {
        echo '<option value="' . _h($v['subjectCode']) . '"' . selected($subjectCode, _h($v['subjectCode']), false) . '>' . _h($v['subjectCode']) . ' ' . _h($v['subjectName']) . '</option>' . "\n";
    }
}

/**
 * Faculty dropdown: shows general list of faculty and
 * if $facID is not NULL, shows the faculty attached 
 * to a particular record.
 * 
 * @since 1.0.0
 * @param string $facID - optional
 * @return string Returns the record id if selected is true.
 */
function facID_dropdown($facID = NULL)
{
    $app = \Liten\Liten::getInstance();
    $fac = $app->db->query("SELECT staffID FROM staff_meta WHERE staffType = 'FAC' ORDER BY staffID");
    $q = $fac->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    foreach ($q as $k => $v) {
        echo '<option value="' . _h($v['staffID']) . '"' . selected($facID, _h($v['staffID']), false) . '>' . get_name(_h($v['staffID'])) . '</option>' . "\n";
    }
}

/**
 * Payment type dropdown: shows general list of payment types and
 * if $typeID is not NULL, shows the payment type attached 
 * to a particular record.
 * 
 * @since 1.0.3
 * @param string $typeID - optional
 * @return string Returns the record id if selected is true.
 */
function payment_type_dropdown($typeID = NULL)
{
    $app = \Liten\Liten::getInstance();
    $pay = $app->db->query('SELECT * FROM payment_type');
    $q = $pay->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $k => $v) {
        echo '<option value="' . _h($v['ptID']) . '"' . selected($typeID, _h($v['ptID']), false) . '>' . _h($v['type']) . '</option>' . "\n";
    }
}

/**
 * Table dropdown: pulls dropdown list from specified table
 * if $tableID is not NULL, shows the record attached 
 * to a particular record.
 * 
 * @since 1.0.0
 * @param string $table
 * @param string $where
 * @param string $code
 * @param string $name
 * @param string $activeCode
 * @return mixed
 */
function table_dropdown($table, $where = null, $id, $code, $name, $activeID = null, $bind = null)
{
    $app = \Liten\Liten::getInstance();
    $table = $app->db->$table()
        ->select("$id,$code,$name");
    if ($where !== null && $bind == null) {
        $table->where($where);
    }
    if ($bind !== null) {
        $table->where($where, $bind);
    }
    $q = $table->find(function($data) {
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
    $email = $app->db->query("SELECT 
                        a.email,a.personID 
                    FROM person a 
                    LEFT JOIN staff b ON a.personID = b.staffID 
                    WHERE b.status = 'A' 
                    ORDER BY lname");
    $q = $email->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $k => $v) {
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
        $array = [];
        $q = $app->db->query("SELECT * FROM $table WHERE $column = ?", [$id]);
        foreach ($q as $r) {
            $array[] = $r;
        }
        $date = explode("-", $r[$field]);
    }

    /* years */
    $html_output = '           <select name="' . $name . 'Year"' . $bool . ' class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">' . "\n";
    $html_output .= '               <option value="">&nbsp;</option>' . "\n";
    for ($year = 2000; $year <= (date("Y") - $limit); $year++) {
        $html_output .= '               <option value="' . sprintf("%04s", $year) . '"' . selected(sprintf("%04s", $year), $date[0], false) . '>' . sprintf("%04s", $year) . '</option>' . "\n";
    }
    $html_output .= '           </select>' . "\n";

    /* months */
    $html_output .= '           <select name="' . $name . 'Month"' . $bool . ' class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">' . "\n";
    $html_output .= '               <option value="">&nbsp;</option>' . "\n";
    $months = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    for ($month = 1; $month <= 12; $month++) {
        $html_output .= '               <option value="' . sprintf("%02s", $month) . '"' . selected(sprintf("%02s", $month), $date[1], false) . '>' . $months[$month] . '</option>' . "\n";
    }
    $html_output .= '           </select>' . "\n";

    /* days */
    $html_output .= '           <select name="' . $name . 'Day"' . $bool . ' class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">' . "\n";
    $html_output .= '               <option value="">&nbsp;</option>' . "\n";
    for ($day = 1; $day <= 31; $day++) {
        $html_output .= '               <option value="' . sprintf("%02s", $day) . '"' . selected(sprintf("%02s", $day), $date[2], false) . '>' . sprintf("%02s", $day) . '</option>' . "\n";
    }
    $html_output .= '           </select>' . "\n";

    return $html_output;
}

/**
 * A function which returns true if the logged in user
 * is a student in the system.
 * @since 4.3
 * @param $id
 * @return mixed
 */
function isStudent($id)
{
    $app = \Liten\Liten::getInstance();
    $stu = $app->db->student()->where('stuID = ?', $id);
    $q = $stu->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    if (count($q) > 0) {
        return true;
    }
    return false;
}

/**
 * A function which returns true if the logged in user
 * has an active student, staff, or faculty record.
 * @since 4.3
 * @param $id
 * @return mixed
 */
function isRecordActive($id)
{
    $app = \Liten\Liten::getInstance();
    $rec = $app->db->query("SELECT 
				a.personID 
			FROM person a 
			LEFT JOIN student b ON a.personID = b.stuID 
			LEFT JOIN staff c ON a.personID = c.staffID 
			WHERE a.personID = ? 
			AND b.status = 'A' 
			OR c.status = 'A'", [$id]
    );
    $q = $rec->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    if (count($q) > 0) {
        return true;
    }
    return false;
}

/**
 * If the logged in user is not a student,
 * hide the menu item. For myeduTrac usage.
 * 
 * @since 4.3
 * @param $id int(required)
 * @return mixed
 */
function checkStuMenuAccess($id)
{
    if (!isStudent($id)) {
        return ' style="display:none !important;"';
    }
}

/**
 * If the logged in user is not a student,
 * redirect the user to his/her profile.
 * 
 * @since 4.3
 * @param $id int(required)
 * @return mixed
 */
function checkStuAccess($id)
{
    return isStudent($id);
}

function studentsExist($id)
{
    $app = \Liten\Liten::getInstance();
    $stu = $app->db->query("SELECT * FROM stu_course_sec WHERE courseSecID = ?", [$id]);
    $q = $stu->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    if (count($q[0]['id']) > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * @since 4.0.7
 */
function getstudentload($term, $creds, $level)
{
    $app = \Liten\Liten::getInstance();
    $t = explode("/", $term);
    $newTerm1 = $t[0];
    $newTerm2 = $t[1];
    $sql = $app->db->query("SELECT 
                        status 
                    FROM student_load_rule 
                    WHERE term REGEXP CONCAT('[[:<:]]', ?, '[[:>:]]') 
                    OR term REGEXP CONCAT('[[:<:]]', ?, '[[:>:]]') 
                    AND acadLevelCode REGEXP CONCAT('[[:<:]]', ?, '[[:>:]]') 
                    AND ? 
                    BETWEEN min_cred 
                    AND max_cred 
                    AND active = '1'", [$newTerm1, $newTerm2, $level, $creds]
    );
    $q = $sql->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        return $r['status'];
    }
}

function supervisor($id, $active = NULL)
{
    $app = \Liten\Liten::getInstance();
    $s = $app->db->query("SELECT 
                        staffID  
                    FROM staff 
                    WHERE staffID != ?", [$id]
    );
    $q = $s->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    foreach ($q as $k => $v) {
        echo '<option value="' . _h($v['staffID']) . '"' . selected($active, _h($v['staffID']), false) . '>' . get_name(_h($v['staffID'])) . '</option>' . "\n";
    }
}

function getJobID()
{
    $app = \Liten\Liten::getInstance();
    $job = $app->db->query('SELECT jobID FROM staff_meta WHERE staffID = ? AND endDate = NULL or endDate = "0000-00-00"', [get_persondata('personID')]);
    $q = $job->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        return _h($r['jobID']);
    }
}

function getJobTitle()
{
    $app = \Liten\Liten::getInstance();
    $job = $app->db->query("SELECT 
                        a.title 
                    FROM job a 
                    LEFT JOIN staff_meta b 
                    ON a.ID = b.jobID 
                    WHERE a.ID = ?", [ getJobID()]
    );
    $q = $job->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        return _h($r['title']);
    }
}

function getStaffJobTitle($id)
{
    $app = \Liten\Liten::getInstance();
    $title = $app->db->query("SELECT 
                        a.title 
                    FROM job a 
                    LEFT JOIN staff_meta b 
                    ON a.ID = b.jobID 
                    WHERE b.staffID = ? 
                    AND b.hireDate = (SELECT MAX(hireDate) FROM staff_meta WHERE staffID = ?)", [ $id, $id]
    );
    $q = $title->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    foreach ($q as $r) {
        return _h($r['title']);
    }
}

function rolePerm($id)
{
    $app = \Liten\Liten::getInstance();
    $role = $app->db->query("SELECT permission from role WHERE ID = ?", [$id]);
    $q1 = $role->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    $a = [];
    foreach ($q1 as $v) {
        $a[] = $v;
    }
    $sql = $app->db->query("SELECT * FROM permission");
    $q2 = $sql->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q2 as $r) {
        $perm = $app->hook->maybe_unserialize($v['permission']);
        echo '
				<tr>
					<td>' . $r['permName'] . '</td>
					<td class="text-center">';
        if (in_array($r['permKey'], $perm)) {
            echo '<input type="checkbox" name="permission[]" value="' . $r['permKey'] . '" checked="checked" />';
        } else {
            echo '<input type="checkbox" name="permission[]" value="' . $r['permKey'] . '" />';
        }
        echo '</td>
            </tr>';
    }
}

function personPerm($id)
{
    $app = \Liten\Liten::getInstance();
    $array = [];
    $pp = $app->db->query("SELECT permission FROM person_perms WHERE personID = ?", [$id]);
    $q = $pp->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        $array[] = $r;
    }
    $personPerm = $app->hook->{'maybe_unserialize'}($r['permission']);
    /**
     * Select the role(s) of the person who's 
     * personID = $id
     */
    $array1 = [];
    $pr = $app->db->query("SELECT roleID from person_roles WHERE personID = ?", [$id]);
    $q1 = $pr->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q1 as $r1) {
        $array1[] = $r1;
    }
    /**
     * Select all the permissions from the role(s)
     * that are connected to the selected person.
     */
    $array2 = [];
    $role = $app->db->query("SELECT permission from role WHERE ID = ?", [_h($r1['roleID'])]);
    $q2 = $role->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q2 as $r2) {
        $array2[] = $r2;
    }
    $perm = $app->hook->{'maybe_unserialize'}($r2['permission']);
    $permission = $app->db->query("SELECT * FROM permission");
    $sql = $permission->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($sql as $row) {
        echo '
            <tr>
                <td>' . $row['permName'] . '</td>
                <td class="text-center">';
        if (in_array($row['permKey'], $perm)) {
            echo '<input type="checkbox" name="permission[]" value="' . $row['permKey'] . '" checked="checked" disabled="disabled" />';
        } elseif ($personPerm != '' && in_array($row['permKey'], $personPerm)) {
            echo '<input type="checkbox" name="permission[]" value="' . $row['permKey'] . '" checked="checked" />';
        } else {
            echo '<input type="checkbox" name="permission[]" value="' . $row['permKey'] . '" />';
        }
        echo '</td>
            </tr>';
    }
}

function student_has_restriction()
{
    $app = \Liten\Liten::getInstance();
    $rest = $app->db->query("SELECT 
        				GROUP_CONCAT(DISTINCT c.deptName SEPARATOR ',') AS 'Restriction' 
    				FROM restriction a 
					LEFT JOIN restriction_code b ON a.rstrCode = b.rstrCode 
					LEFT JOIN department c ON b.deptCode = c.deptCode 
					WHERE a.severity = '99' 
					AND a.endDate <= '0000-00-00' 
					AND a.stuID = ? 
					GROUP BY a.stuID 
					HAVING a.stuID = ?", [ get_persondata('personID'), get_persondata('personID')]
    );
    $q = $rest->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    if (count($q[0]['Restriction']) > 0) {
        foreach ($q as $r) {
            return '<strong>' . $r['Restriction'] . '</strong>';
        }
    } else {
        return false;
    }
}

/**
 * @since 4.5
 */
function is_count_zero($table, $field, $ID)
{
    $app = \Liten\Liten::getInstance();
    $zero = $app->db->query("SELECT $field FROM $table WHERE $field = ?", [$ID]);
    $q = $zero->find(function($data) {
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
 * is_ferpa function added to check for 
 * active FERPA restrictions for students.
 * 
 * @since 4.5
 */
function is_ferpa($id)
{
    $app = \Liten\Liten::getInstance();
    $ferpa = $app->db->query("SELECT 
                        rstrID 
                    FROM restriction 
                    WHERE stuID = ? 
                    AND rstrCode = 'FERPA' 
                    AND (endDate = '' OR endDate = '0000-00-00')", [$id]
    );
    $q = $ferpa->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    if (count($q) > 0) {
        return 'Yes';
    } else {
        return 'No';
    }
}

/**
 * Function wrapper for the setError log method.
 */
function logError($type, $string, $file, $line)
{
    $log = new \app\src\Log;
    return $log->setError($type, $string, $file, $line);
}

function translate_class_year($year)
{
    switch ($year) {
        case 'FR':
            return 'Freshman';
            break;

        case 'SO':
            return 'Sophomore';
            break;

        case 'JR':
            return 'Junior';
            break;

        case 'SR':
            return 'Senior';
            break;

        case 'GR':
            return 'Grad Student';
            break;

        case 'PhD':
            return 'PhD Student';
            break;
    }
}

function translate_addr_status($status)
{
    switch ($status) {
        case 'C':
            return 'Current';
            break;

        case 'I':
            return 'Inactive';
            break;
    }
}

function translate_addr_type($type)
{
    switch ($type) {
        case 'H':
            return 'Home';
            break;

        case 'P':
            return 'Permanent';
            break;

        case 'B':
            return 'Business';
            break;
    }
}

function get_name($ID)
{
    $app = \Liten\Liten::getInstance();
    $name = $app->db->query("SELECT lname, fname FROM person WHERE personID = ?", [$ID]);
    $q = $name->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        $array[] = $r;
    }
    return _h($r['lname']) . ', ' . _h($r['fname']);
}

/**
 * @since 4.1.6
 */
function get_initials($ID, $initials = 2)
{
    $app = \Liten\Liten::getInstance();
    $name = $app->db->person()->select('lname,fname')->where('personID = ?', $ID);
    $q = $name->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        if ($initials == 2) {
            return substr(_h($r['fname']), 0, 1) . '. ' . substr(_h($r['lname']), 0, 1) . '.';
        } else {
            return _h($r['lname']) . ', ' . substr(_h($r['fname']), 0, 1) . '.';
        }
    }
}

function hasAppl($id)
{
    $app = \Liten\Liten::getInstance();
    $appl = $app->db->query("SELECT * FROM application WHERE personID = ?", [$id]);
    $q = $appl->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    $a = [];
    foreach ($q as $r) {
        $a[] = $r;
    }
    return _h($r['personID']);
}

function getStuSec($code, $term)
{
    $app = \Liten\Liten::getInstance();
    $stcs = $app->db->stu_course_sec()
        ->where('stuID = ?', get_persondata('personID'))->_and_()
        ->where('courseSecCode = ?', $code)->_and_()
        ->where('termCode = ?', $term);

    $q = $stcs->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    if (count($q[0]['id']) > 0) {
        return ' style="display:none;"';
    }
}

function isRegistrationOpen()
{
    $app = \Liten\Liten::getInstance();
    if ($app->hook->get_option('open_registration') == 0 || !isStudent(get_persondata('personID'))) {
        return ' style="display:none !important;"';
    }
}

/**
 * Graduated Status: if the status on a student's program 
 * is "G", then the status and status dates are disabled.
 * 
 * @since 1.0.0
 * @param string
 * @return mixed
 */
function gs($s)
{
    if ($s == 'G') {
        return ' readonly="readonly"';
    }
}
/* Calculate grade points for stu_acad_cred. */

function acadCredGradePoints($grade, $credits)
{
    $app = \Liten\Liten::getInstance();
    $gp = $app->db->query("SELECT points FROM grade_scale WHERE grade = ?", [$grade]);
    $q = $gp->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        $gradePoints = $r['points'] * $credits;
    }
    return $gradePoints;
}

/**
 * Function to help with SQL injection when using SQL terminal 
 * and the saved query screens.
 */
function strstra($haystack, $needles = array(), $before_needle = false)
{
    $chr = array();
    foreach ($needles as $needle) {
        $res = strstr($haystack, $needle, $before_needle);
        if ($res !== false)
            $chr[$needle] = $res;
    }
    if (empty($chr))
        return false;
    return min($chr);
}

function print_gzipped_page()
{

    global $HTTP_ACCEPT_ENCODING;
    if (headers_sent()) {
        $encoding = false;
    } elseif (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false) {
        $encoding = 'x-gzip';
    } elseif (strpos($HTTP_ACCEPT_ENCODING, 'gzip') !== false) {
        $encoding = 'gzip';
    } else {
        $encoding = false;
    }

    if ($encoding) {
        $contents = ob_get_contents();
        ob_end_clean();
        header('Content-Encoding: ' . $encoding);
        print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
        $size = strlen($contents);
        $contents = gzcompress($contents, 9);
        $contents = substr($contents, 0, $size);
        print($contents);
        exit();
    } else {
        ob_end_flush();
        exit();
    }
}

function student_can_register()
{
    $app = \Liten\Liten::getInstance();
    $stcs = $app->db->query("SELECT 
                        COUNT(courseSecCode) AS Courses 
                    FROM stu_course_sec 
                    WHERE stuID = ? 
                    AND termCode = ? 
                    AND status IN('A','N') 
                    GROUP BY stuID,termCode", [ get_persondata('personID'), $app->hook->get_option('registration_term')]
    );
    $q = $stcs->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        $courses = $r['Courses'];
    }

    $rstr = $app->db->query("SELECT * 
                    FROM restriction 
                    WHERE severity = '99' 
                    AND stuID = ? 
                    AND endDate = '0000-00-00' 
                    OR endDate > ?", [ get_persondata('personID'), date('Y-m-d')]
    );
    $sql1 = $rstr->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    $stu = $app->db->query("SELECT 
        				a.ID 
    				FROM 
    					student a 
					LEFT JOIN 
						stu_program b 
					ON 
						a.stuID = b.stuID 
					WHERE 
						a.stuID = ? 
					AND 
						a.status = 'A' 
					AND 
						b.currStatus = 'A'", [ get_persondata('personID')]
    );

    $sql2 = $stu->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    if ($courses != NULL && $courses >= $app->hook->{'get_option'}('number_of_courses')) {
        return false;
    } elseif (count($sql1[0]['rstrID']) > 0) {
        return false;
    } elseif (count($sql2[0]['ID']) <= 0) {
        return false;
    } else {
        return true;
    }
}

/**
 * Checks to see if there is a preReq on
 * the course the student is registering for.
 * If there is one, then we do a check to see
 * if the student has meet the preReq.
 */
function prerequisite($stuID, $courseSecID)
{
    $app = \Liten\Liten::getInstance();
    $crse = $app->db->query("SELECT 
    					a.preReq 
					FROM course a 
					LEFT JOIN course_sec b ON a.courseID = b.courseID 
					WHERE b.courseSecID = ?", [$courseSecID]
    );
    $q1 = $crse->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    $array = [];
    foreach ($q1 as $r1) {
        $array[] = $r1;
    }
    $req = explode(",", $r1['preReq']);
    if (count($q1[0]['preReq']) > 0) {
        $stac = $app->db->query("SELECT 
	    					stuAcadCredID 
						FROM stu_acad_cred 
						WHERE courseCode IN('" . str_replace(",", "', '", $r1['preReq']) . "')
						AND stuID = ? 
						AND status IN('A','N') 
						AND grade <> '' 
						AND grade <> 'W' 
						AND grade <> 'I' 
						AND grade <> 'F' 
						GROUP BY stuID,courseCode", [$stuID]
        );
        $q2 = $stac->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
    }
    if (empty($r1['preReq']) || count($req) == count($q2[0]['stuAcadCredID'])) {
        return true;
    }
}

/**
 * Function for retrieving a person's
 * uploaded school photo.
 * 
 * @since 4.5
 */
function getSchoolPhoto($id, $email, $s)
{
    $app = \Liten\Liten::getInstance();
    $photo = $app->db->query('SELECT photo FROM person WHERE personID = ? AND photo <> "" AND photo <> "NULL"', [$id]);
    $q = $photo->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        $photo = _h($r['photo']);
    }
    $url = url('/') . 'static/photos/' . $photo;
    $photosize = getimagesize($url);
    if (count($q) > 0) {
        if (getPathInfo('/form/photo/') === '/form/photo/') {
            $avatar = '<a href="' . url('/') . 'form/deleteSchoolPhoto/"><img src="' . url('/') . 'static/photos/' . $photo . '" ' . imgResize($photosize[1], $photosize[1], $s) . ' alt="' . get_name($id) . '" class="thumb" /></a>';
        } else {
            $avatar = '<img src="' . url('/') . 'static/photos/' . $photo . '" ' . imgResize($photosize[1], $photosize[1], $s) . ' alt="' . get_name($id) . '" class="thumb" />';
        }
    } else {
        $avatar = get_user_avatar($email, $s, 'thumb');
    }
    return $avatar;
}

function percent($num_amount, $num_total)
{
    $count1 = $num_amount / $num_total;
    $count2 = $count1 * 100;
    $count = number_format($count2, 0);
    return $count;
}

/**
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout eduTrac to allow for both string or array
 * to be merged into another array.
 *
 * @since 4.2.0
 * @param string|array $args     Value to merge with $defaults
 * @param array        $defaults Optional. Array that serves as the defaults. Default empty.
 * @return array Merged user defined values with defaults.
 */
function et_parse_args($args, $defaults = '')
{
    if (is_object($args))
        $r = get_object_vars($args);
    elseif (is_array($args))
        $r = & $args;
    else
        et_parse_str($args, $r);

    if (is_array($defaults))
        return array_merge($defaults, $r);
    return $r;
}

function upgradeSQL($file, $delimiter = ';')
{
    $app = \Liten\Liten::getInstance();
    set_time_limit(0);

    if (is_file($file) === true) {
        $file = fopen($file, 'r');

        if (is_resource($file) === true) {
            $query = [];

            while (feof($file) === false) {
                $query[] = fgets($file);

                if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1) {
                    $query = trim(implode('', $query));

                    if ($app->db->query($query) === false) {
                        echo '<p><font color="red">ERROR:</font> ' . $query . '</p>' . "\n";
                    } else {
                        echo '<p><font color="green">SUCCESS:</font> ' . $query . '</p>' . "\n";
                    }

                    while (ob_get_level() > 0) {
                        ob_end_flush();
                    }

                    flush();
                }

                if (is_string($query) === true) {
                    $query = [];
                }
            }

            fclose($file);
            redirect(url('/dashboard/upgrade/'));
        }
    }
}

function redirect_upgrade_db()
{
    $app = \Liten\Liten::getInstance();
    $acl = new \app\src\ACL(get_persondata('personID'));
    if ($acl->userHasRole(8)) {
        if (RELEASE_TAG == getCurrentRelease()) {
            if ($app->hook->get_option('dbversion') < upgradeDB()) {
                if (basename($_SERVER["REQUEST_URI"]) != "upgrade") {
                    redirect(url('/dashboard/upgrade/'));
                }
            }
        }
    }
}

function head_release_meta()
{
    echo "<meta name='generator' content='eduTrac ERP " . CURRENT_RELEASE . "'>\n";
}

function foot_release()
{
    if (CURRENT_RELEASE != RELEASE_TAG) {
        echo "r" . CURRENT_RELEASE . ' (t' . RELEASE_TAG . ')';
    } else {
        echo "r" . CURRENT_RELEASE;
    }
}

function et_hash_password($password)
{
    // By default, use the portable hash from phpass
    $hasher = new \app\src\PasswordHash(8, FALSE);

    return $hasher->HashPassword($password);
}

function et_check_password($password, $hash, $person_id = '')
{
    $app = \Liten\Liten::getInstance();
    // If the hash is still md5...
    if (strlen($hash) <= 32) {
        $check = ( $hash == md5($password) );
        if ($check && $person_id) {
            // Rehash using new hash.
            et_set_password($password, $person_id);
            $hash = et_hash_password($password);
        }
        return $app->hook->{'apply_filter'}('check_password', $check, $password, $hash, $person_id);
    }

    // If the stored hash is longer than an MD5, presume the
    // new style phpass portable hash.
    $hasher = new \app\src\PasswordHash(8, FALSE);

    $check = $hasher->CheckPassword($password, $hash);

    return $app->hook->{'apply_filter'}('check_password', $check, $password, $hash, $person_id);
}

function et_set_password($password, $person_id)
{
    $app = \Liten\Liten::getInstance();
    $hash = et_hash_password($password);
    $q = $app->db->person();
    $q->password = $hash;
    $q->where('personID = ?', $person_id)->update();
}

function et_hash_cookie($cookie)
{
    // By default, use the portable hash from phpass
    $hasher = new \app\src\PasswordHash(8, TRUE);

    return $hasher->HashPassword($cookie);
}

function et_authenticate_cookie($cookie, $cookiehash, $person_id = '')
{
    $app = \Liten\Liten::getInstance();

    $hasher = new \app\src\PasswordHash(8, TRUE);

    $check = $hasher->CheckPassword($cookie, $cookiehash);

    return $app->hook->{'apply_filter'}('authenticate_cookie', $check, $cookie, $cookiehash, $person_id);
}

/**
 * Prints a list of timezones which includes
 * current time.
 * 
 * @return array
 */
function generate_timezone_list()
{
    static $regions = array(
        \DateTimeZone::AFRICA,
        \DateTimeZone::AMERICA,
        \DateTimeZone::ANTARCTICA,
        \DateTimeZone::ASIA,
        \DateTimeZone::ATLANTIC,
        \DateTimeZone::AUSTRALIA,
        \DateTimeZone::EUROPE,
        \DateTimeZone::INDIAN,
        \DateTimeZone::PACIFIC,
    );

    $timezones = array();
    foreach ($regions as $region) {
        $timezones = array_merge($timezones, \DateTimeZone::listIdentifiers($region));
    }

    $timezone_offsets = array();
    foreach ($timezones as $timezone) {
        $tz = new \DateTimeZone($timezone);
        $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
    }

    // sort timezone by timezone name
    ksort($timezone_offsets);

    $timezone_list = array();
    foreach ($timezone_offsets as $timezone => $offset) {
        $offset_prefix = $offset < 0 ? '-' : '+';
        $offset_formatted = gmdate('H:i', abs($offset));

        $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

        $t = new \DateTimeZone($timezone);
        $c = new \DateTime(null, $t);
        $current_time = $c->format('g:i A');

        $timezone_list[$timezone] = "(${pretty_offset}) $timezone - $current_time";
    }

    return $timezone_list;
}

/**
 * Get age by birthdate.
 */
function getAge($birthdate = '0000-00-00')
{
    if ($birthdate == '0000-00-00')
        return 'Unknown';

    $bits = explode('-', $birthdate);
    $age = date('Y') - $bits[0] - 1;

    $arr[1] = 'm';
    $arr[2] = 'd';

    for ($i = 1; $arr[$i]; $i++) {
        $n = date($arr[$i]);
        if ($n < $bits[$i])
            break;
        if ($n > $bits[$i]) {
            ++$age;
            break;
        }
    }
    return $age;
}

/**
 * Converts a string into unicode values.
 * 
 * @since 4.3
 * @param string $string
 * @return mixed
 */
function unicoder($string)
{
    $p = str_split(trim($string));
    $new_string = '';
    foreach ($p as $val) {
        $new_string .= '&#' . ord($val) . ';';
    }
    return $new_string;
}

/**
 * Retrieve requested field from person table 
 * based on user's id.
 *
 * @since 3.0.2
 * @return mixed
 * 
 */
function getUserValue($id, $field)
{
    $app = \Liten\Liten::getInstance();
    $value = $app->db->person()
        ->select($field)
        ->where('personID = ?', $id);
    $q = $value->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        return $r[$field];
    }
}

/**
 * Checks against certain keywords when the SQL 
 * terminal and saved query screens are used. Helps 
 * against database manipulation and SQL injection.
 * 
 * @since 1.0.0
 * @return boolean
 */
function forbidden_keyword()
{
    $array = [
        "create", "delete", "drop table", "alter",
        "insert", "change", "convert", "modifies",
        "optimize", "purge", "rename", "replace",
        "revoke", "unlock", "truncate", "anything",
        "svc", "write", "into", "--", "1=1", "1 = 1", "\\",
        "?", "'x'", "loop", "exit", "leave", "undo",
        "upgrade", "html", "script", "css",
        "x=x", "x = x", "everything", "anyone", "everyone",
        "upload", "&", "&amp;", "xp_", "$", "0=0", "0 = 0",
        "X=X", "X = X", "mysql", "'='", "XSS", "mysql_",
        "die", "password", "auth_token", "alert", "img", "src",
        "drop tables", "drop index", "drop database", "drop column",
        "show tables in", "show databases", " in ",
        "slave", "hosts", "grants", "warnings", "variables",
        "triggers", "privileges", "engine", "processlist",
        "relaylog", "errors", "information_schema", "mysqldump",
        "hostname", "root", "use", "describe", "flush", "privileges",
        "mysqladmin", "set", "quit", "-u", "-p", "load data",
        "backup table", "cache index", "change master to", "commit",
        "drop user", "drop view", "kill", "load index", "load table",
        "lock", "reset", "restore", "rollback", "savepoint",
        "show character set", "show collation", "innodb",
        "show table status"
    ];
    return $array;
}

/**
 * The myeduTrac welcome message filter.
 * 
 * @since 4.3
 */
function the_myet_welcome_message()
{
    $app = \Liten\Liten::getInstance();
    $welcome_message = $app->hook->{'get_option'}('myet_welcome_message');
    $welcome_message = $app->hook->{'apply_filter'}('the_myet_welcome_message', $welcome_message);
    $welcome_message = str_replace(']]>', ']]&gt;', $welcome_message);
    return $welcome_message;
}

/**
 * @since 4.4
 */
function shoppingCart()
{
    $app = \Liten\Liten::getInstance();
    $cart = $app->db->stu_rgn_cart()
        ->where('stuID = ?', get_persondata('personID'));
    $q = $cart->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    if (count($q[0]['stuID']) > 0) {
        return true;
    }
}

/**
 * @since 4.4
 */
function removeFromCart($section)
{
    $app = \Liten\Liten::getInstance();
    $cart = $app->db->stu_rgn_cart()
        ->where('stuID = ?', get_persondata('personID'))->_and_()
        ->whereGte('deleteDate', $app->db->NOW())->_and_()
        ->where('courseSecID = ?', $section);
    $q = $cart->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    if (count($q[0]['stuID']) > 0) {
        return true;
    }
}

/**
 * @since 4.4
 */
function convertCourseSec($sect)
{
    $app = \Liten\Liten::getInstance();
    $section = $app->db->course_sec()
        ->select('courseSecCode')
        ->where('courseSecID = ?', $sect);
    $q = $section->find(function($data) {
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

<?php
if (! defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * eduTrac SIS Person Functions
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();

/**
 * A function which returns true if the logged in person
 * has an active student, staff, or faculty record.
 *
 * @since 4.3
 * @param int $id
 *            Person ID.
 * @return bool
 */
function isRecordActive($id)
{
    if ('' == _trim($id)) {
        $message = _t('Invalid person ID: empty ID given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }
    
    if (! is_numeric($id)) {
        $message = _t('Invalid person ID: person id must be numeric.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }
    
    $app = \Liten\Liten::getInstance();
    $rec = $app->db->person()
        ->select('person.personID')
        ->_join('student', 'person.personID = student.stuID')
        ->_join('staff', 'person.personID = staff.staffID')
        ->where('person.personID = ?', $id)
        ->_and_()
        ->where('student.status = "A"')
        ->_or_()
        ->where('staff.status = "A"')
        ->findOne();
    
    if ($rec !== false) {
        return true;
    }
    return false;
}

function rolePerm($id)
{
    $app = \Liten\Liten::getInstance();
    $role = $app->db->query("SELECT permission from role WHERE ID = ?", [
        $id
    ]);
    $q1 = $role->find(function ($data) {
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
    $sql = $app->db->permission();
    $q2 = $sql->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q2 as $r) {
        $perm = maybe_unserialize($v['permission']);
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
    $pp = $app->db->query("SELECT permission FROM person_perms WHERE personID = ?", [
        $id
    ]);
    $q = $pp->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        $array[] = $r;
    }
    $personPerm = maybe_unserialize($r['permission']);
    /**
     * Select the role(s) of the person who's
     * personID = $id
     */
    $array1 = [];
    $pr = $app->db->query("SELECT roleID from person_roles WHERE personID = ?", [
        $id
    ]);
    $q1 = $pr->find(function ($data) {
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
    $role = $app->db->query("SELECT permission from role WHERE ID = ?", [
        _h($r1['roleID'])
    ]);
    $q2 = $role->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q2 as $r2) {
        $array2[] = $r2;
    }
    $perm = maybe_unserialize($r2['permission']);
    $permission = $app->db->permission();
    $sql = $permission->find(function ($data) {
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

/**
 * Returns the name of a particular person.
 *
 * @since 1.0.0
 * @param int $ID
 *            Person ID.
 * @return string
 */
function get_name($ID)
{
    if ('' == _trim($ID)) {
        $message = _t('Invalid person ID: empty ID given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }
    
    if (! is_numeric($ID)) {
        $message = _t('Invalid person ID: person id must be numeric.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }
    
    $name = get_person_by('personID', $ID);
    
    return _h($name->lname) . ', ' . _h($name->fname);
}

/**
 * Shows selected person's initials instead of
 * his/her's full name.
 *
 * @since 4.1.6
 * @param int $ID
 *            Person ID
 * @param int $initials
 *            Number of initials to show.
 * @return string
 */
function get_initials($ID, $initials = 2)
{
    if ('' == _trim($ID)) {
        $message = _t('Invalid person ID: empty ID given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }
    
    if (! is_numeric($ID)) {
        $message = _t('Invalid person ID: person id must be numeric.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }
    
    $name = get_person_by('personID', $ID);
    
    if ($initials == 2) {
        return substr(_h($name->fname), 0, 1) . '. ' . substr(_h($name->lname), 0, 1) . '.';
    } else {
        return _h($name->lname) . ', ' . substr(_h($name->fname), 0, 1) . '.';
    }
}

/**
 * Function for retrieving a person's
 * uploaded school photo.
 *
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
    $app = \Liten\Liten::getInstance();
    
    $nae = $app->db->person()
        ->select('photo')
        ->where('personID = ?', $id)
        ->_and_()
        ->where('photo <> ""')
        ->_and_()
        ->where('photo <> "NULL"')
        ->findOne();
    
    if ($nae !== false) {
        $photosize = getimagesize(get_base_url() . 'static/photos/' . $nae->photo);
        if (getPathInfo('/form/photo/') === '/form/photo/') {
            $avatar = '<a href="' . get_base_url() . 'form/deleteSchoolPhoto/"><img src="' . get_base_url() . 'static/photos/' . $nae->photo . '" ' . imgResize($photosize[1], $photosize[1], $s) . ' alt="' . get_name($id) . '" class="' . $class . '" /></a>';
        } else {
            $avatar = '<img src="' . get_base_url() . 'static/photos/' . $nae->photo . '" ' . imgResize($photosize[1], $photosize[1], $s) . ' alt="' . get_name($id) . '" class="' . $class . '" />';
        }
    } else {
        $avatar = get_user_avatar($email, $s, $class);
    }
    return $avatar;
}

/**
 * Retrieve requested field from person table
 * based on user's id.
 *
 * @since 3.0.2
 * @param int $id
 *            Person ID.
 * @param mixed $field
 *            Data requested of particular person.
 * @return mixed
 */
function getUserValue($id, $field)
{
    $value = get_person_by('personID', $id);
    
    return $value->$field;
}

/**
 * Retrieves a list of roles from the roles table.
 *
 * @since 6.0.04
 * @return mixed
 */
function get_perm_roles()
{
    $app = \Liten\Liten::getInstance();
    $query = $app->db->query('SELECT
    		trim(leading "0" from ID) AS roleID, roleName
		FROM role');
    $result = $query->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    
    foreach ($result as $r) {
        echo '<option value="' . _h($r['roleID']) . '">' . _h($r['roleName']) . '</option>' . "\n";
    }
}

/**
 * Retrieves person data given a person ID or person array.
 *
 * @since 6.2.0
 * @param int|etsis_Person|null $person
 *            Person ID or person array.
 * @param bool $object
 *            If set to true, data will return as an object, else as an array.
 */
function get_person($person, $object = true)
{
    if ($person instanceof \app\src\Core\etsis_Person) {
        $_person = $person;
    } elseif (is_array($person)) {
        if (empty($person['personID'])) {
            $_person = new \app\src\Core\etsis_Person($person);
        } else {
            $_person = \app\src\Core\etsis_Person::get_instance($person['personID']);
        }
    } else {
        $_person = \app\src\Core\etsis_Person::get_instance($person);
    }
    
    if (! $_person) {
        return null;
    }
    
    if ($object == true) {
        $_person = array_to_object($_person);
    }
    
    return $_person;
}

/**
 * Checks whether the given username exists.
 *
 * @since 6.2.4
 * @param string $username
 *            Username to check.
 * @return int|false The person's ID on success, and false on failure.
 */
function username_exists($username)
{
    if ($person = get_person_by('uname', $username)) {
        return $person->personID;
    }
    return false;
}

/**
 * Checks whether the given email exists.
 *
 * @since 6.2.4
 * @param string $email
 *            Email to check.
 * @return int|false The person's ID on success, and false on failure.
 */
function email_exists($email)
{
    if ($person = get_person_by('email', $email)) {
        return $person->personID;
    }
    return false;
}
<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;

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
    $app = \Liten\Liten::getInstance();

    if ('' == _trim($id)) {
        $message = _t('Invalid person ID: empty ID given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    if (!is_numeric($id)) {
        $message = _t('Invalid person ID: person id must be numeric.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }
    try {
        $rec = $app->db->person()
            ->select('person.personID')
            ->_join('student', 'person.personID = student.stuID')
            ->_join('staff', 'person.personID = staff.staffID')
            ->where('person.personID = ?', $id)->_and_()
            ->where('student.status = "A"')->_or_()
            ->where('staff.status = "A"')
            ->findOne();

        if ($rec !== false) {
            return true;
        }
        return false;
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
}

function rolePerm($id)
{
    $app = \Liten\Liten::getInstance();
    try {
        $role = $app->db->role()
            ->select('permission')
            ->where('id = ?', $id);
        $q1 = $role->find(function ($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        foreach ($q1 as $v) {
            $perm = maybe_unserialize(_escape($v['permission']));
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
            echo '
				<tr>
					<td>' . _h($r['permName']) . '</td>
					<td class="text-center">';
            if (is_array($perm) && in_array(_h($r['permKey']), $perm)) {
                echo '<input type="checkbox" name="permission[]" value="' . _h($r['permKey']) . '" checked="checked" />';
            } else {
                echo '<input type="checkbox" name="permission[]" value="' . _h($r['permKey']) . '" />';
            }
            echo '</td>
            </tr>';
        }
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
}

function personPerm($id)
{
    $app = \Liten\Liten::getInstance();
    try {
        $array = [];
        $pp = $app->db->person_perms()
            ->select('permission')
            ->where('personID = ?', $id);
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
        $personPerm = maybe_unserialize(_escape($r['permission']));
        /**
         * Select the role(s) of the person who's
         * personID = $id
         */
        $array1 = [];
        $pr = $app->db->person_roles()
            ->select('roleID')
            ->where('personID = ?', $id);
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
        $role = $app->db->role()
            ->select('permission')
            ->where('id = ?', $r1['roleID']);
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
        $perm = maybe_unserialize(_escape($r2['permission']));
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
                <td>' . _h($row['permName']) . '</td>
                <td class="text-center">';
            if (is_array($perm) && in_array(_h($row['permKey']), $perm)) {
                echo '<input type="checkbox" name="permission[]" value="' . _h($row['permKey']) . '" checked="checked" disabled="disabled" />';
            } elseif ($personPerm != '' && in_array(_h($row['permKey']), $personPerm)) {
                echo '<input type="checkbox" name="permission[]" value="' . _h($row['permKey']) . '" checked="checked" />';
            } else {
                echo '<input type="checkbox" name="permission[]" value="' . _h($row['permKey']) . '" />';
            }
            echo '</td>
            </tr>';
        }
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
}

/**
 * Returns the name of a particular person.
 *
 * @since 1.0.0
 * @param int $id
 *            Person ID.
 * @return string
 */
function get_name($id)
{
    if ('' == _trim($id)) {
        $message = _t('Invalid person ID: empty ID given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    if (!is_numeric($id)) {
        $message = _t('Invalid person ID: person id must be numeric.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    $name = get_person_by('personID', $id);

    return _h($name->lname) . ', ' . _h($name->fname);
}

/**
 * Shows selected person's initials instead of
 * his/her's full name.
 *
 * @since 4.1.6
 * @param int $id
 *            Person ID
 * @param int $initials
 *            Number of initials to show.
 * @return string
 */
function get_initials($id, $initials = 2)
{
    if ('' == _trim($id)) {
        $message = _t('Invalid person ID: empty ID given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    if (!is_numeric($id)) {
        $message = _t('Invalid person ID: person id must be numeric.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    $name = get_person_by('personID', $id);

    if ($initials == 2) {
        return mb_substr(_h($name->fname), 0, 1, 'UTF-8') . '. ' . mb_substr(_h($name->lname), 0, 1, 'UTF-8') . '.';
    } else {
        return _h($name->lname) . ', ' . mb_substr(_h($name->fname), 0, 1, 'UTF-8') . '.';
    }
}

/**
 * Function for retrieving a person's
 * uploaded school photo.
 *
 * @since 6.3.0
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
function get_school_photo($id, $email, $s = 80, $class = 'thumb')
{
    $app = \Liten\Liten::getInstance();
    try {
        $nae = $app->db->person()
            ->select('photo')
            ->where('personID = ?', $id)->_and_()
            ->where('photo <> ""')->_and_()
            ->where('photo <> "NULL"')
            ->findOne();

        if ($nae !== false) {
            $photosize = getimagesize(get_base_url() . 'static/photos/' . _h($nae->photo));
            if (get_path_info('/form/photo/') === '/form/photo/') {
                $avatar = '<a href="' . get_base_url() . 'form/deleteSchoolPhoto/"><img src="' . get_base_url() . 'static/photos/' . _h($nae->photo) . '" ' . imgResize($photosize[1], $photosize[1], $s) . ' alt="' . get_name($id) . '" class="' . $class . '" /></a>';
            } else {
                $avatar = '<img src="' . get_base_url() . 'static/photos/' . _h($nae->photo) . '" ' . imgResize($photosize[1], $photosize[1], $s) . ' alt="' . get_name($id) . '" class="' . $class . '" />';
            }
        } else {
            $avatar = get_user_avatar($email, $s, $class);
        }
        return $avatar;
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
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
function get_user_value($id, $field)
{
    $value = get_person_by('personID', $id);

    return _h($value->{$field});
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
    try {
        $query = $app->db->query('SELECT
    		trim(leading "0" from id) AS roleID, roleName
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
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
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

    if (!$_person) {
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
        return _h($person->personID);
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
        return _h($person->personID);
    }
    return false;
}

/**
 * Retrieve alternate ID if it exists.
 * 
 * @since 6.3.0
 * @param int $id Person's unique system id.
 * @return int Alt ID or system id.
 */
function get_alt_id($id)
{
    $app = \Liten\Liten::getInstance();
    try {
        $person = $app->db->person()
            ->select('personID,altID')
            ->where('personID = ?', $id)
            ->findOne();

        if (_h($person->altID) != '') {
            return _h($person->altID);
        } else {
            return _h($person->personID);
        }
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
}

/**
 * Checks if a person has an active restriction with 99 severity.
 * 
 * @since 6.3.0
 * @return mixed
 */
function person_has_restriction()
{
    $app = \Liten\Liten::getInstance();
    try {
        $rest = $app->db->query("SELECT
        				GROUP_CONCAT(DISTINCT c.deptName SEPARATOR ',') AS 'Restriction'
    				FROM perc 
					LEFT JOIN rest b ON perc.code = b.code
					LEFT JOIN department c ON b.deptCode = c.deptCode
					WHERE perc.severity = '99'
                    AND perc.personID = ?
					AND perc.endDate IS NULL
					OR perc.endDate <= '0000-00-00'
					GROUP BY perc.personID
					HAVING perc.personID = ?", [
            get_persondata('personID'),
            get_persondata('personID')
        ]);
        $q = $rest->find(function ($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        if (count($q[0]['Restriction']) > 0) {
            foreach ($q as $r) {
                return '<strong>' . _h($r['Restriction']) . '</strong>';
            }
        } else {
            return false;
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
 * is_ferpa function added to check for
 * active FERPA restrictions for person/student.
 *
 * @since 4.5
 * @param int $id
 *            Person/Student's ID.
 */
function is_ferpa($id)
{
    $app = \Liten\Liten::getInstance();

    if ('' == _trim($id)) {
        $message = _t('Invalid person/student ID: empty ID given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    if (!is_numeric($id)) {
        $message = _t('Invalid person/student ID: person/student id must be numeric.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    try {
        $ferpa = $app->db->perc()
            ->where('personID = ?', $id)->_and_()
            ->where('code = "FERPA"')->_and_()
            ->where('endDate IS NULL')->_or_()
            ->whereLte('endDate', '0000-00-00')
            ->count('id');

        if ($ferpa > 0) {
            return _t('Yes');
        } else {
            return _t('No');
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

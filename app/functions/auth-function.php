<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use Cascade\Cascade;

/**
 * eduTrac Auth Helper
 *
 * @since 3.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
function hasPermission($perm)
{
    $acl = new \app\src\ACL(get_persondata('personID'));

    if ($acl->hasPermission($perm) && is_user_logged_in()) {
        return true;
    } else {
        return false;
    }
}

function get_persondata($field)
{
    $app = \Liten\Liten::getInstance();
    $person = get_secure_cookie_data('ET_COOKIENAME');
    $value = $app->db->person()
        ->select('person.*,address.*,staff.*,student.*')
        ->_join('address', 'person.personID = address.personID')
        ->_join('staff', 'person.personID = staff.staffID')
        ->_join('student', 'person.personID = student.stuID')
        ->where('person.personID = ?', $person->personID)->_and_()
        ->where('person.uname = ?', $person->uname);
    $q = $value->find(function ($data) {
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

/**
 * Checks if a visitor is logged in or not.
 * 
 * @since 6.2.10
 * @return boolean
 */
function is_user_logged_in()
{
    $app = \Liten\Liten::getInstance();

    $person = get_person_by('personID', get_persondata('personID'));

    if ('' != $person->personID && $app->cookies->verifySecureCookie('ET_COOKIENAME')) {
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

/**
 * Hide element function.
 *
 * This is an alternative to the hl() function which may become
 * deprecated in a later release.
 *
 * @since 6.2.0
 * @param string $permission
 *            Permission to check for.
 * @return bool
 */
function _he($permission)
{
    if (hasPermission($permission)) {
        return true;
    }

    return false;
}

/**
 * Module function.
 *
 * This is an alternative to the ml() function which may become
 * depreated in a later release.
 *
 * @since 6.2.0
 * @param string $function_name
 *            Function to check for.
 * @return bool
 */
function _mf($function_name)
{
    if (function_exists($function_name)) {
        return true;
    }

    return false;
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
 * Course Sec disable select dropdowns.
 */
function csid()
{
    if (hasRestriction('course_sec_inquiry_only')) {
        return ' disabled';
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
 * Academic Program disable select dropdowns.
 */
function apid()
{
    if (hasRestriction('acad_prog_inquiry_only')) {
        return ' disabled';
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

/**
 * Retrieve person info by a given field from the person's table.
 *
 * @since 6.2.0
 * @param string $field The field to retrieve the user with.
 * @param int|string $value A value for $field (personID, altID, uname or email).
 */
function get_person_by($field, $value)
{
    $app = \Liten\Liten::getInstance();

    $person = $app->db->person()
        ->select('person.*, address.*, staff.*, student.*')
        ->_join('address', 'person.personID = address.personID')
        ->_join('staff', 'person.personID = staff.staffID')
        ->_join('student', 'person.personID = student.stuID')
        ->where("person.$field = ?", $value)
        ->findOne();

    return $person;
}

/**
 * Logs a person in after the login information has checked out.
 *
 * @since 6.2.0
 * @param string $login Person's username or email address.
 * @param string $password Person's password.
 * @param string $rememberme Whether to remember the person.
 */
function etsis_authenticate($login, $password, $rememberme)
{
    $app = \Liten\Liten::getInstance();

    $person = $app->db->person()
        ->select('person.personID,person.uname,person.password')
        ->_join('staff', 'person.personID = staff.staffID')
        ->_join('student', 'person.personID = student.stuID')
        ->where('(person.uname = ? OR person.email = ?)', [$login, $login])->_and_()
        ->where('(staff.status = "A" OR student.status = "A")')
        ->findOne();

    if (false == $person) {
        $app->flash('error_message', sprintf(_t('Your account is not active. <a href="%s">More info.</a>'), 'https://www.edutracsis.com/manual/troubleshooting/#Your_Account_is_Deactivated'));
        redirect($app->req->server['HTTP_REFERER']);
        return;
    }

    $ll = $app->db->person();
    $ll->LastLogin = $ll->NOW();
    $ll->where('personID = ?', _h($person->personID))->update();
    /**
     * Filters the authentication cookie.
     * 
     * @since 6.2.0
     * @param object $person Person data object.
     * @param string $rememberme Whether to remember the person.
     * @throws Exception If $person is not a database object.
     */
    try {
        $app->hook->apply_filter('etsis_auth_cookie', $person, $rememberme);
    } catch (\app\src\Core\Exception\Exception $e) {
        Cascade\Cascade::getLogger('error')->error($e->getMessage());
    }

    etsis_logger_activity_log_write('Authentication', 'Login', get_name(_h($person->personID)), _h($person->uname));
    redirect(get_base_url());
}

/**
 * Checks a person's login information.
 *
 * @since 6.2.0
 * @param string $login Person's username or email address.
 * @param string $password Person's password.
 * @param string $rememberme Whether to remember the person.
 */
function etsis_authenticate_person($login, $password, $rememberme)
{
    $app = \Liten\Liten::getInstance();

    if (empty($login) || empty($password)) {

        if (empty($login)) {
            $app->flash('error_message', _t('<strong>ERROR</strong>: The username/email field is empty.'));
        }

        if (empty($password)) {
            $app->flash('error_message', _t('<strong>ERROR</strong>: The password field is empty.'));
        }

        redirect(get_base_url() . 'login' . '/');
        return;
    }

    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $person = get_person_by('email', $login);

        if (false == $person->email) {
            $app->flash('error_message', _t('<strong>ERROR</strong>: Invalid email address.'));

            redirect(get_base_url() . 'login' . '/');
            return;
        }
    } else {
        $person = get_person_by('uname', $login);

        if (false == $person->uname) {
            $app->flash('error_message', _t('<strong>ERROR</strong>: Invalid username.'));

            redirect(get_base_url() . 'login' . '/');
            return;
        }
    }

    if (!etsis_check_password($password, $person->password, _h($person->personID))) {
        $app->flash('error_message', _t('<strong>ERROR</strong>: The password you entered is incorrect.'));

        redirect(get_base_url() . 'login' . '/');
        return;
    }

    /**
     * Filters log in details.
     * 
     * @since 6.2.0
     * @param string $login Person's username or email address.
     * @param string $password Person's password.
     * @param string $rememberme Whether to remember the person.
     */
    $person = $app->hook->apply_filter('etsis_authenticate_person', $login, $password, $rememberme);

    return $person;
}

function etsis_set_auth_cookie($person, $rememberme = '')
{

    $app = \Liten\Liten::getInstance();

    if (!is_object($person)) {
        throw new \app\src\Core\Exception\Exception(_t('"$person" should be a database object.'), 'set_auth_cookie');
    }

    if (isset($rememberme)) {
        /**
         * Ensure the browser will continue to send the cookie until it expires.
         * 
         * @since 6.2.0
         */
        $expire = $app->hook->apply_filter('auth_cookie_expiration', (_h(get_option('cookieexpire')) !== '') ? _h(get_option('cookieexpire')) : $app->config('cookies.lifetime'));
    } else {
        /**
         * Ensure the browser will continue to send the cookie until it expires.
         *
         * @since 6.2.0
         */
        $expire = $app->hook->apply_filter('auth_cookie_expiration', ($app->config('cookies.lifetime') !== '') ? $app->config('cookies.lifetime') : 86400);
    }

    $auth_cookie = [
        'key' => 'ET_COOKIENAME',
        'personID' => _h($person->personID),
        'uname' => _h($person->uname),
        'remember' => (isset($rememberme) ? $rememberme : _t('no')),
        'exp' => $expire + time()
    ];

    /**
     * Fires immediately before the secure authentication cookie is set.
     *
     * @since 6.2.0
     * @param string $auth_cookie Authentication cookie.
     * @param int    $expire  Duration in seconds the authentication cookie should be valid.
     */
    $app->hook->do_action('set_auth_cookie', $auth_cookie, $expire);

    $app->cookies->setSecureCookie($auth_cookie);
}

/**
 * Removes all cookies associated with authentication.
 * 
 * @since 6.2.0
 */
function etsis_clear_auth_cookie()
{

    $app = \Liten\Liten::getInstance();

    /**
     * Fires just before the authentication cookies are cleared.
     *
     * @since 6.2.0
     */
    $app->hook->do_action('clear_auth_cookie');

    $vars1 = [];
    parse_str($app->cookies->get('ET_COOKIENAME'), $vars1);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file1 = $app->config('cookies.savepath') . 'cookies.' . $vars1['data'];
    try {
        if (etsis_file_exists($file1)) {
            unlink($file1);
        }
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error(sprintf('FILESTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    }

    $vars2 = [];
    parse_str($app->cookies->get('SWITCH_USERBACK'), $vars2);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file2 = $app->config('cookies.savepath') . 'cookies.' . $vars2['data'];
    if (etsis_file_exists($file2, false)) {
        unlink($file2);
    }

    /**
     * After the cookie is removed from the server,
     * we know need to remove it from the browser and
     * redirect the user to the login page.
     */
    $app->cookies->remove('ET_COOKIENAME');
    $app->cookies->remove('SWITCH_USERBACK');
}

/**
 * Shows error messages on login form.
 * 
 * @since 6.2.5
 */
function etsis_login_form_show_message()
{
    $app = \Liten\Liten::getInstance();
    $flash = new \app\src\Core\etsis_Messages();
    echo $app->hook->apply_filter('login_form_show_message', $flash->showMessage());
}

/**
 * Retrieves data from a secure cookie.
 * 
 * @since 6.2.12
 * @param string $key COOKIE key.
 * @return mixed
 */
function get_secure_cookie_data($key)
{
    $app = \Liten\Liten::getInstance();
    $data = $app->cookies->getSecureCookie($key);
    return $data;
}

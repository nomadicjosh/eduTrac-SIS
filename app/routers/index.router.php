<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;

/**
 * Index Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$hasher = new \app\src\PasswordHash(8, FALSE);

/**
 * Before route check.
 */
$app->before('GET|POST', '/', function() {
    if (_h(get_option('enable_myet_portal')) == 0 && !hasPermission('edit_myet_css')) {
        redirect(get_base_url() . 'offline' . '/');
    }
});

$app->get('/', function () use($app) {

    $app->view->display('index/index');
});

$app->before('GET|POST', '/spam/', function() use($app) {
    if (_h(get_option('enable_myet_portal')) == 0 && !hasPermission('edit_myet_css')) {
        redirect(get_base_url() . 'offline' . '/');
    }

    if (empty($app->req->server['HTTP_REFERER'])) {
        redirect(get_base_url());
    }
});

$app->get('/spam/', function () use($app) {

    $app->view->display('index/spam');
});

$app->get('/offline/', function () use($app) {

    $app->view->display('index/offline');
});

$app->before('GET|POST', '/online-app/', function() {
    if (_h(get_option('enable_myet_portal')) == 0 && !hasPermission('edit_myet_css')) {
        redirect(get_base_url() . 'offline' . '/');
    }
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/login/', function() {
    if (is_user_logged_in()) {
        redirect(get_base_url() . 'profile' . '/');
    }
});

$app->match('GET|POST', '/login/', function () use($app) {

    if ($app->req->isPost()) {
        /**
         * This function is documented in app/functions/auth-function.php.
         * 
         * @since 6.2.0
         */
        etsis_authenticate_person($app->req->post['uname'], $app->req->post['password'], $app->req->post['rememberme']);
    }

    $app->view->display('index/login', [
        'title' => 'Login'
        ]
    );
});

$app->post('/reset-password/', function () use($app) {

    if ($app->req->isPost()) {
        try {
            $addr = $app->req->_post('email');
            $name = $app->req->_post('name');
            $body = $app->req->_post('message');
            $message = process_email_html($body, _t("Reset Password Request"));
            $headers = "From: $name <$addr>\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            if (_etsis_email()->etsis_mail(_h(get_option('system_email')), _t("Reset Password Request"), $message, $headers)) {
                _etsis_flash()->success(_t('Your request has been sent.'), $app->req->server['HTTP_REFERER']);
            } else {
                _etsis_flash()->error(_t('System encountered an error. Please try again.'), $app->req->server['HTTP_REFERER']);
            }
        } catch (phpmailerException $e) {
            _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
        }
    }
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/profile/', function() {
    if (!is_user_logged_in()) {
        redirect(get_base_url() . 'login' . '/');
    }
});

$app->get('/profile/', function () use($app) {

    try {
        $profile = $app->db->query("SELECT 
								personID,prefix,uname,fname,lname,mname,email,ssn,ethnicity,
								dob,emergency_contact,emergency_contact_phone,
							CASE veteran 
							WHEN '1' THEN 'Yes' 
							ELSE 'No' END AS 'Veteran',
							CASE gender 
							WHEN 'M' THEN 'Male'
							ELSE 'Female' END AS 'Gender'
							FROM person 
							WHERE personID = ?", [get_persondata('personID')]
        );
        $q1 = $profile->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $addr = $app->db->address()
            ->setTableAlias('a')
            ->_join('address', 'a.personID = b.personID', 'b')
            ->where('a.personID = ?', get_persondata('personID'))->_and_()
            ->where('b.addressType = "P"')->_and_()
            ->where('b.endDate = "0000-00-00"')->_and_()
            ->where('b.addressStatus = "C"');
        $q2 = $addr->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
    } catch (NotFoundException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (ORMException $e) {
        _etsis_flash()->error($e->getMessage());
    }

    $app->view->display('index/profile', [
        'title' => 'My Profile',
        'profile' => $q1,
        'addr' => $q2
        ]
    );
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/password/', function() {
    if (!is_user_logged_in()) {
        redirect(get_base_url() . 'login' . '/');
    }
});

$app->match('GET|POST', '/password/', function () use($app) {
    if ($app->req->isPost()) {
        try {
            $pass = $app->db->person()->select('personID,password')
                ->where('personID = ?', get_persondata('personID'));
            $q = $pass->find(function($data) {
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
            if (etsis_check_password($app->req->post['currPass'], $r['password'], $r['personID'])) {
                $sql = $app->db->person();
                $sql->password = etsis_hash_password($app->req->post['newPass']);
                $sql->where('personID = ?', get_persondata('personID'));
                if ($sql->update()) {
                    /**
                     * @since 6.1.07
                     */
                    $pass = [];
                    $pass['pass'] = $app->req->post['newPass'];
                    $pass['personID'] = get_persondata('personID');
                    $pass['uname'] = get_persondata('uname');
                    $pass['fname'] = get_persondata('fname');
                    $pass['lname'] = get_persondata('lname');
                    $pass['email'] = get_persondata('email');
                    /**
                     * Fires after password was updated successfully.
                     * 
                     * @since 6.1.07
                     * @param string $pass Plaintext password submitted by logged in user.
                     */
                    $app->hook->do_action('post_change_password', $pass);

                    _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                } else {
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }
    }

    $app->view->display('index/password', [
        'title' => 'Change Password'
        ]
    );
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/permission.*', function() {
    if (!hasPermission('access_permission_screen')) {
        _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->match('GET|POST', '/permission/', function () use($app) {

    etsis_register_style('form');
    etsis_register_style('table');
    etsis_register_script('select');
    etsis_register_script('select2');
    etsis_register_script('datatables');

    $app->view->display('permission/index', [
        'title' => 'Manage Permissions',
        ]
    );
});

$app->match('GET|POST', '/permission/(\d+)/', function ($id) use($app, $json_url, $flashNow) {
    if ($app->req->isPost()) {
        try {
            $perm = $app->db->permission();
            foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                $perm->$k = $v;
            }
            $perm->where('ID = ?', $id);
            if ($perm->update()) {
                etsis_logger_activity_log_write('Update Record', 'Permission', _filter_input_string(INPUT_POST, 'permName'), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } else {
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }
    }

    try {
        $perm = $app->db->permission()->where('ID = ?', $id)->findOne();
    } catch (NotFoundException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (ORMException $e) {
        _etsis_flash()->error($e->getMessage());
    }

    /**
     * If the database table doesn't exist, then it
     * is false and a 404 should be sent.
     */
    if ($perm == false) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If the query is legit, but there
     * is no data in the table, then 404
     * will be shown.
     */ elseif (empty($perm) == true) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If data is zero, 404 not found.
     */ elseif (count($perm->ID) <= 0) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If we get to this point, the all is well
     * and it is ok to process the query and print
     * the results in a html format.
     */ else {

        etsis_register_style('form');
        etsis_register_script('select');
        etsis_register_script('select2');

        $app->view->display('permission/view', [
            'title' => 'Edit Permission',
            'perm' => $perm
            ]
        );
    }
});

$app->match('GET|POST', '/permission/add/', function () use($app, $flashNow) {

    if ($app->req->isPost()) {
        try {
            $perm = $app->db->permission();
            foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                $perm->$k = $v;
            }
            if ($perm->save()) {
                etsis_logger_activity_log_write('New Record', 'Permission', _filter_input_string(INPUT_POST, 'permName'), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'permission' . '/');
            } else {
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }
    }

    etsis_register_style('form');
    etsis_register_script('select');
    etsis_register_script('select2');

    $app->view->display('permission/add', [
        'title' => 'Add New Permission'
        ]
    );
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/role.*', function() {
    if (!hasPermission('access_role_screen')) {
        _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->match('GET|POST', '/role/', function () use($app) {

    etsis_register_style('form');
    etsis_register_style('table');
    etsis_register_script('select');
    etsis_register_script('select2');
    etsis_register_script('datatables');

    $app->view->display('role/index', [
        'title' => 'Manage Roles'
        ]
    );
});

$app->match('GET|POST', '/role/(\d+)/', function ($id) use($app) {
    try {
        $role = $app->db->role()->where('ID = ?', $id)->findOne();
    } catch (NotFoundException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (ORMException $e) {
        _etsis_flash()->error($e->getMessage());
    }

    /**
     * If the database table doesn't exist, then it
     * is false and a 404 should be sent.
     */
    if ($role == false) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If the query is legit, but there
     * is no data in the table, then 404
     * will be shown.
     */ elseif (empty($role) == true) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If data is zero, 404 not found.
     */ elseif (count($role->ID) <= 0) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If we get to this point, the all is well
     * and it is ok to process the query and print
     * the results in a html format.
     */ else {

        etsis_register_style('form');
        etsis_register_script('select');
        etsis_register_script('select2');

        $app->view->display('role/view', [
            'title' => 'Edit Role',
            'role' => $role
            ]
        );
    }
});

$app->match('GET|POST', '/role/add/', function () use($app) {

    if ($app->req->isPost()) {
        try {
            $roleID = $app->req->post['roleID'];
            $roleName = $app->req->post['roleName'];
            $rolePerm = maybe_serialize($app->req->post['permission']);

            $strSQL = $app->db->query(sprintf("REPLACE INTO `role` SET `ID` = %u, `roleName` = '%s', `permission` = '%s'", $roleID, $roleName, $rolePerm));
            if ($strSQL) {
                $ID = $strSQL->lastInsertId();
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'role' . '/' . $ID . '/');
            } else {
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }
    }

    etsis_register_style('form');
    etsis_register_script('select');
    etsis_register_script('select2');

    $app->view->display('role/add', [
        'title' => 'Add Role'
        ]
    );
});

$app->post('/role/editRole/', function () use($app) {
    try {
        $roleID = $app->req->post['roleID'];
        $roleName = $app->req->post['roleName'];
        $rolePerm = maybe_serialize($app->req->post['permission']);

        $strSQL = $app->db->query(sprintf("REPLACE INTO `role` SET `ID` = %u, `roleName` = '%s', `permission` = '%s'", $roleID, $roleName, $rolePerm));
        if ($strSQL) {
            _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
        } else {
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        }
    } catch (NotFoundException $e) {
        _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
    } catch (ORMException $e) {
        _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
    }
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/message/', function() {
    if (!is_user_logged_in()) {
        redirect(get_base_url() . 'dashboard' . '/');
        exit();
    }
});

$app->post('/message/', function () use($app) {
    $options = ['myet_welcome_message'];

    foreach ($options as $option_name) {
        if (!isset($app->req->post[$option_name]))
            continue;
        $value = $app->req->post[$option_name];
        update_option($option_name, $value);
    }
    /**
     * Fired when updating options for options_meta table.
     * 
     * @return mixed
     */
    $app->hook->do_action('myetsis_welcome_message_option');
    /* Write to logs */
    etsis_logger_activity_log_write('Update', 'myetSIS', 'Welcome Message', get_persondata('uname'));

    redirect($app->req->server['HTTP_REFERER']);
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/switchUserTo/(\d+)/', function() {
    if (!hasPermission('login_as_user')) {
        _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->get('/switchUserTo/(\d+)/', function ($id) use($app) {

    if (isset($_COOKIE['ETSIS_COOKIENAME'])) {
        $switch_cookie = [
            'key' => 'SWITCH_USERBACK',
            'personID' => get_persondata('personID'),
            'uname' => get_persondata('uname'),
            'remember' => (_h(get_option('cookieexpire')) - time() > 86400 ? _t('yes') : _t('no')),
            'exp' => _h(get_option('cookieexpire')) + time()
        ];
        $app->cookies->setSecureCookie($switch_cookie);
    }

    $vars = [];
    parse_str($app->cookies->get('ETSIS_COOKIENAME'), $vars);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file = $app->config('cookies.savepath') . 'cookies.' . $vars['data'];
    try {
        if (etsis_file_exists($file)) {
            unlink($file);
        }
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error(sprintf('FILESTATE[%s]: File not found: %s', $e->getCode(), $e->getMessage()));
    }

    /**
     * Delete the old cookie.
     */
    $app->cookies->remove("ETSIS_COOKIENAME");

    $auth_cookie = [
        'key' => 'ETSIS_COOKIENAME',
        'personID' => $id,
        'uname' => getUserValue($id, 'uname'),
        'remember' => (_h(get_option('cookieexpire')) - time() > 86400 ? _t('yes') : _t('no')),
        'exp' => _h(get_option('cookieexpire')) + time()
    ];

    $app->cookies->setSecureCookie($auth_cookie);

    _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
});

$app->get('/switchUserBack/(\d+)/', function ($id) use($app) {
    $vars1 = [];
    parse_str($app->cookies->get('ETSIS_COOKIENAME'), $vars1);
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
        Cascade::getLogger('error')->error(sprintf('FILESTATE[%s]: File not found: %s', $e->getCode(), $e->getMessage()));
    }

    $app->cookies->remove("ETSIS_COOKIENAME");

    $vars2 = [];
    parse_str($app->cookies->get('SWITCH_USERBACK'), $vars2);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file2 = $app->config('cookies.savepath') . 'cookies.' . $vars2['data'];
    try {
        if (etsis_file_exists($file2)) {
            unlink($file2);
        }
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error(sprintf('FILESTATE[%s]: File not found: %s', $e->getCode(), $e->getMessage()));
    }

    $app->cookies->remove("SWITCH_USERBACK");

    /**
     * After the login as user cookies have been
     * removed from the server and the browser,
     * we need to set fresh cookies for the
     * original logged in user.
     */
    $switch_cookie = [
        'key' => 'ETSIS_COOKIENAME',
        'personID' => $id,
        'uname' => getUserValue($id, 'uname'),
        'remember' => (_h(get_option('cookieexpire')) - time() > 86400 ? _t('yes') : _t('no')),
        'exp' => _h(get_option('cookieexpire')) + time()
    ];
    $app->cookies->setSecureCookie($switch_cookie);
    _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
});

$app->get('/logout/', function () {

    etsis_logger_activity_log_write('Authentication', 'Logout', get_name(get_persondata('personID')), get_persondata('uname'));
    /**
     * This function is documented in app/functions/auth-function.php.
     * 
     * @since 6.2.0
     */
    etsis_clear_auth_cookie();

    redirect(get_base_url() . 'login' . '/');
});

$app->setError(function() use($app) {

    $app->view->display('error/404', [
        'title' => '404 Error'
        ]
    );
});

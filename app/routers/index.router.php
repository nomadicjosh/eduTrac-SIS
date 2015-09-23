<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Index Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$json_url = url('/api/');
$hasher = new \app\src\PasswordHash(8, FALSE);

$logger = new \app\src\Log();
$cache = new \app\src\Cache();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();

/**
 * Before route check.
 */
$app->before('GET|POST', '/', function() {
    if (!file_exists(BASE_PATH . 'config.php')) {
        redirect(url('/install/?step=1'));
    }
});

$app->before('GET|POST', '/', function() use($app) {
    if (_h(get_option('enable_myet_portal') == 0) && !hasPermission('edit_myet_css')) {
        redirect(url('/offline/'));
    }
});

$app->get('/', function () use($app) {

    $app->view->display('index/index');
});

$app->before('GET|POST', '/spam/', function() use($app) {
    if (_h(get_option('enable_myet_portal') == 0) && !hasPermission('edit_myet_css')) {
        redirect(url('/offline/'));
    }

    if (empty($app->req->server['HTTP_REFERER'])) {
        redirect(url('/'));
    }
});

$app->get('/spam/', function () use($app) {

    $app->view->display('index/spam');
});

$app->get('/offline/', function () use($app) {

    $app->view->display('index/offline');
});

$app->before('GET|POST', '/online-app/', function() use($app) {
    if (_h(get_option('enable_myet_portal') == 0) && !hasPermission('edit_myet_css')) {
        redirect(url('/offline/'));
    }
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/login/', function() {
    if (isUserLoggedIn()) {
        redirect(url('/profile/'));
    }
});

$app->match('GET|POST', '/login/', function () use($app, $hasher, $logger) {

    if ($app->req->isPost()) {
        $person = $app->db->person()
            ->select('person.personID,person.uname,person.password')
            ->_join('staff','person.personID = staff.staffID')
            ->_join('student','person.personID = student.stuID')
            ->where('person.uname = ?', $app->req->_post('uname'))->_and_()
            ->where('(staff.status = "A" OR student.status = "A")');
        $q = $person->find(function($data) {
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
        
        if(count($q) <= 0) {
            $app->flash('error_message', 'Your account is deactivated.');
            redirect($app->req->server['HTTP_REFERER']);
            exit();
        }

        /**
         * Checks if the submitted username exists in
         * the database.
         */
        if ($app->req->_post('uname') !== _h($r['uname'])) {
            $app->flash('error_message', 'The username does not exist. Please try again.');
            redirect(url('/login/'));
            return;
        }

        /**
         * Checks if the password is correct.
         */
        if (et_check_password($app->req->_post('password'), $r['password'], _h($r['personID']))) {
            $ll = $app->db->person();
            $ll->LastLogin = $ll->NOW();
            $ll->where('personID = ?', _h($r['personID']))->update();
            if (isset($_POST['rememberme'])) {
                $app->cookies->setSecureCookie('ET_COOKNAME', _h($r['personID']), (get_option('cookieexpire') !== '') ? get_option('cookieexpire') : $app->config('cookie.lifetime'));
                $app->cookies->setSecureCookie('ET_REMEMBER', 'rememberme', (get_option('cookieexpire') !== '') ? get_option('cookieexpire') : $app->config('cookie.lifetime'));
            } else {
                $app->cookies->setSecureCookie('ET_COOKNAME', _h($r['personID']), ($app->config('cookie.lifetime') !== '') ? $app->config('cookie.lifetime') : 86400);
            }
            $logger->setLog('Authentication', 'Login', get_name(_h($r['personID'])), _h($r['uname']));
            redirect(url('/'));
        } else {
            $app->flash('error_message', 'The password you entered was incorrect.');
            redirect(url('/login/'));
        }
    }

    $app->view->display('index/login', [
        'title' => 'Login'
        ]
    );
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/profile/', function() {
    if (!isUserLoggedIn()) {
        redirect(url('/login/'));
    }
});

$app->get('/profile/', function () use($app) {

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
    if (!isUserLoggedIn()) {
        redirect(url('/login/'));
    }
});

$app->match('GET|POST', '/password/', function () use($app, $flashNow) {
    if ($app->req->isPost()) {
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
        if (et_check_password($_POST['currPass'], $r['password'], $r['personID'])) {
            $sql = $app->db->person();
            $sql->password = et_hash_password($_POST['newPass']);
            $sql->where('personID = ?', get_persondata('personID'));
            if ($sql->update()) {
                $app->flash('success_message', $flashNow->notice(200));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
        }
        redirect($app->req->server['HTTP_REFERER']);
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
        redirect(url('/dashboard/'));
    }
});

$app->match('GET|POST', '/permission/', function () use($app) {
    $css = [ 'css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
    $js = [
        'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/jasny-fileupload/assets/js/bootstrap-fileupload.js?v=v2.1.0'
    ];


    $app->view->display('permission/index', [
        'title' => 'Manage Permissions',
        'cssArray' => $css,
        'jsArray' => $js
        ]
    );
});

$app->match('GET|POST', '/permission/(\d+)/', function ($id) use($app, $json_url, $logger, $cache, $flashNow) {
    if ($app->req->isPost()) {
        $perm = $app->db->permission();
        foreach (_filter_input_array(INPUT_POST) as $k => $v) {
            $perm->$k = $v;
        }
        $perm->where('ID = ?', $id);
        if ($perm->update()) {
            $cache->clearCache('permission');
            $app->flash('success_message', $flashNow->notice(200));
            $logger->setLog('Update Record', 'Permission', _filter_input_string(INPUT_POST, 'permName'), get_persondata('uname'));
        } else {
            $app->flash('error_message', $flashNow->notice(409));
        }
        redirect($app->req->server['HTTP_REFERER']);
    }

    $json = _file_get_contents($json_url . 'permission/ID/' . $id . '/?key=' . get_option('api_key'));
    $decode = json_decode($json, true);

    $css = [ 'css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
    $js = [
        'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/jasny-fileupload/assets/js/bootstrap-fileupload.js?v=v2.1.0'
    ];

    /**
     * If the database table doesn't exist, then it
     * is false and a 404 should be sent.
     */
    if ($decode == false) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If the query is legit, but there
     * is no data in the table, then 404
     * will be shown.
     */ elseif (empty($decode) == true) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If data is zero, 404 not found.
     */ elseif (count($decode[0]['ID']) <= 0) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If we get to this point, the all is well
     * and it is ok to process the query and print
     * the results in a html format.
     */ else {

        $app->view->display('permission/view', [
            'title' => 'Edit Permission',
            'cssArray' => $css,
            'jsArray' => $js,
            'perm' => $decode
            ]
        );
    }
});

$app->match('GET|POST', '/permission/add/', function () use($app, $flashNow, $cache, $logger) {

    $css = [ 'css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
    $js = [
        'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/jasny-fileupload/assets/js/bootstrap-fileupload.js?v=v2.1.0'
    ];
    
    if ($app->req->isPost()) {
        $perm = $app->db->permission();
        foreach (_filter_input_array(INPUT_POST) as $k => $v) {
            $perm->$k = $v;
        }
        if ($perm->save()) {
            $cache->clearCache('permission');
            $app->flash('success_message', $flashNow->notice(200));
            $logger->setLog('New Record', 'Permission', _filter_input_string(INPUT_POST, 'permName'), get_persondata('uname'));
            redirect( url('/permission/') );
        } else {
            $app->flash('error_message', $flashNow->notice(409));
            redirect($app->req->server['HTTP_REFERER']);
        }
    }


    $app->view->display('permission/add', [
        'title' => 'Add New Permission',
        'cssArray' => $css,
        'jsArray' => $js
        ]
    );
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/role.*', function() {
    if (!hasPermission('access_role_screen')) {
        redirect(url('/dashboard/'));
    }
});

$app->match('GET|POST', '/role/', function () use($app) {
    $css = [ 'css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
    $js = [
        'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/jasny-fileupload/assets/js/bootstrap-fileupload.js?v=v2.1.0'
    ];


    $app->view->display('role/index', [
        'title' => 'Manage Roles',
        'cssArray' => $css,
        'jsArray' => $js
        ]
    );
});

$app->match('GET|POST', '/role/(\d+)/', function ($id) use($app, $json_url) {
    $json = _file_get_contents($json_url . 'role/ID/' . $id . '/?key=' . get_option('api_key'));
    $decode = json_decode($json, true);

    $css = [ 'css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
    $js = [
        'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/jasny-fileupload/assets/js/bootstrap-fileupload.js?v=v2.1.0'
    ];

    /**
     * If the database table doesn't exist, then it
     * is false and a 404 should be sent.
     */
    if ($decode == false) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If the query is legit, but there
     * is no data in the table, then 404
     * will be shown.
     */ elseif (empty($decode) == true) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If data is zero, 404 not found.
     */ elseif (count($decode[0]['ID']) <= 0) {

        $app->view->display('error/404', ['title' => '404 Error']);
    }
    /**
     * If we get to this point, the all is well
     * and it is ok to process the query and print
     * the results in a html format.
     */ else {

        $app->view->display('role/view', [
            'title' => 'Edit Role',
            'cssArray' => $css,
            'jsArray' => $js,
            'role' => $decode
            ]
        );
    }
});

$app->match('GET|POST', '/role/add/', function () use($app, $flashNow) {
    $css = [ 'css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
    $js = [
        'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0'
    ];

    if ($app->req->isPost()) {
        $roleID = $_POST['roleID'];
        $roleName = $_POST['roleName'];
        $rolePerm = maybe_serialize($_POST['permission']);

        $strSQL = $app->db->query(sprintf("REPLACE INTO `role` SET `ID` = %u, `roleName` = '%s', `permission` = '%s'", $roleID, $roleName, $rolePerm));
        if ($strSQL) {
            $ID = $strSQL->lastInsertId();
            $app->flash('success_message', $flashNow->notice(200));
            redirect(url('/role/') . $ID . '/');
        } else {
            $app->flash('error_message', $flashNow->notice(409));
            redirect($app->req->server['HTTP_REFERER']);
        }
    }

    $app->view->display('role/add', [
        'title' => 'Add Role',
        'cssArray' => $css,
        'jsArray' => $js
        ]
    );
});

$app->post('/role/editRole/', function () use($app, $flashNow) {
    $roleID = $_POST['roleID'];
    $roleName = $_POST['roleName'];
    $rolePerm = maybe_serialize($_POST['permission']);

    $strSQL = $app->db->query(sprintf("REPLACE INTO `role` SET `ID` = %u, `roleName` = '%s', `permission` = '%s'", $roleID, $roleName, $rolePerm));
    if ($strSQL) {
        $app->flash('success_message', $flashNow->notice(200));
    } else {
        $app->flash('error_message', $flashNow->notice(409));
    }

    redirect($app->req->server['HTTP_REFERER']);
});

$app->post('/message/', function () use($app, $logger) {
    $options = ['myet_welcome_message'];

    foreach ($options as $option_name) {
        if (!isset($_POST[$option_name]))
            continue;
        $value = $_POST[$option_name];
        update_option($option_name, $value);
    }
    /**
     * Fired when updating options for options_meta table.
     * 
     * @return mixed
     */
    do_action('update_options');
    /* Write to logs */
    $logger->setLog('Update', 'myeduTrac', 'Welcome Message', get_persondata('uname'));

    redirect($app->req->server['HTTP_REFERER']);
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/switchUserTo/(\d+)/', function() {
    if (!hasPermission('login_as_user')) {
        redirect(url('/dashboard/'));
    }
});

$app->get('/switchUserTo/(\d+)/', function ($id) use($app) {

    if (isset($_COOKIE['ET_REMEMBER']) && $app->cookies->getSecureCookie('ET_REMEMBER') === 'rememberme') {
        $app->cookies->setSecureCookie('SWITCH_USERBACK', get_persondata('personID'), (get_option('cookieexpire') !== '') ? get_option('cookieexpire') : $app->config('cookie.lifetime'));
        $app->cookies->setSecureCookie('SWITCH_USERNAME', get_persondata('uname'), (get_option('cookieexpire') !== '') ? get_option('cookieexpire') : $app->config('cookie.lifetime'));
    } else {
        $app->cookies->setSecureCookie('SWITCH_USERBACK', get_persondata('personID'), ($app->config('cookie.lifetime') !== '') ? $app->config('cookie.lifetime') : 86400);
        $app->cookies->setSecureCookie('SWITCH_USERNAME', get_persondata('uname'), ($app->config('cookie.lifetime') !== '') ? $app->config('cookie.lifetime') : 86400);
    }

    $vars = [];
    parse_str($app->cookies->get('ET_COOKNAME'), $vars);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file = $app->config('cookies.savepath') . 'cookies.' . $vars['data'];
    if (file_exists($file)) {
        unlink($file);
    }

    /**
     * Delete the old cookie.
     */
    $app->cookies->remove("ET_COOKNAME");

    if (isset($_COOKIE['ET_REMEMBER']) && $app->cookies->getSecureCookie('ET_REMEMBER') === 'rememberme') {
        $app->cookies->setSecureCookie('ET_COOKNAME', $id, (get_option('cookieexpire') !== '') ? get_option('cookieexpire') : $app->config('cookie.lifetime'));
    } else {
        $app->cookies->setSecureCookie('ET_COOKNAME', $id, ($app->config('cookie.lifetime') !== '') ? $app->config('cookie.lifetime') : 86400);
    }

    redirect(url('/dashboard/'));
});

$app->get('/switchUserBack/(\d+)/', function ($id) use($app) {
    $vars1 = [];
    parse_str($app->cookies->get('ET_COOKNAME'), $vars1);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file1 = $app->config('cookies.savepath') . 'cookies.' . $vars1['data'];
    if (file_exists($file1)) {
        unlink($file1);
    }
    $app->cookies->remove("ET_COOKNAME");

    $vars2 = [];
    parse_str($app->cookies->get('SWITCH_USERBACK'), $vars2);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file2 = $app->config('cookies.savepath') . 'cookies.' . $vars2['data'];
    if (file_exists($file2)) {
        unlink($file2);
    }
    $app->cookies->remove("SWITCH_USERBACK");

    $vars3 = [];
    parse_str($app->cookies->get('SWITCH_USERNAME'), $vars3);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file3 = $app->config('cookies.savepath') . 'cookies.' . $vars3['data'];
    if (file_exists($file3)) {
        unlink($file3);
    }
    $app->cookies->remove("SWITCH_USERNAME");

    /**
     * After the login as user cookies have been
     * removed from the server and the browser,
     * we need to set fresh cookies for the
     * original logged in user.
     */
    if (isset($_COOKIE['ET_REMEMBER']) && $app->cookies->getSecureCookie('ET_REMEMBER') === 'rememberme') {
        $app->cookies->setSecureCookie('ET_COOKNAME', $id, (get_option('cookieexpire') !== '') ? get_option('cookieexpire') : $app->config('cookie.lifetime'));
    } else {
        $app->cookies->setSecureCookie('ET_COOKNAME', $id, ($app->config('cookie.lifetime') !== '') ? $app->config('cookie.lifetime') : 86400);
    }
    redirect(url('/dashboard/'));
});

$app->get('/logout/', function () use($app, $logger) {

    $logger->setLog('Authentication', 'Logout', get_name(get_persondata('personID')), get_persondata('uname'));

    $vars1 = [];
    parse_str($app->cookies->get('ET_COOKNAME'), $vars1);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file1 = $app->config('cookies.savepath') . 'cookies.' . $vars1['data'];
    if (file_exists($file1)) {
        unlink($file1);
    }

    $vars2 = [];
    parse_str($app->cookies->get('SWITCH_USERBACK'), $vars2);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file2 = $app->config('cookies.savepath') . 'cookies.' . $vars2['data'];
    if (file_exists($file2)) {
        unlink($file2);
    }

    $vars3 = [];
    parse_str($app->cookies->get('SWITCH_USERNAME'), $vars3);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file3 = $app->config('cookies.savepath') . 'cookies.' . $vars3['data'];
    if (file_exists($file3)) {
        unlink($file3);
    }

    $vars4 = [];
    parse_str($app->cookies->get('ET_REMEMBER'), $vars4);
    /**
     * Checks to see if the cookie is exists on the server.
     * It it exists, we need to delete it.
     */
    $file4 = $app->config('cookies.savepath') . 'cookies.' . $vars4['data'];
    if (file_exists($file4)) {
        unlink($file4);
    }

    /**
     * After the cookie is removed from the server,
     * we know need to remove it from the browser and
     * redirect the user to the login page.
     */
    $app->cookies->remove('ET_COOKNAME');
    $app->cookies->remove('SWITCH_USERBACK');
    $app->cookies->remove('SWITCH_USERNAME');
    $app->cookies->remove('ET_REMEMBER');
    redirect(url('/login/'));
});

$app->setError(function() use($app) {

    $app->view->display('error/404', [
        'title' => '404 Error'
        ]
    );
});

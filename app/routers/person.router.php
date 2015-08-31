<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Person Router
 *
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$css = [ 'css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
$js = [
    'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
    'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-timepicker/assets/lib/js/bootstrap-timepicker.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-timepicker/assets/custom/js/bootstrap-timepicker.init.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/jCombo/jquery.jCombo.min.js'
];

$json_url = url('/api/');

$logger = new \app\src\Log();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();
$email = new \app\src\Email();

$app->group('/nae', function() use ($app, $css, $js, $json_url, $logger, $dbcache, $flashNow, $email) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/', function() {
        if (!hasPermission('access_person_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/', function () use($app, $css, $js, $json_url) {

        if ($app->req->isPost()) {
            $post = $_POST['nae'];
            $search = $app->db->person()
                ->select('personID,altID,fname,lname,uname,email')
                ->whereLike('CONCAT(person.fname," ",person.lname)', "%$post%")->_or_()
                ->whereLike('CONCAT(person.lname," ",person.fname)', "%$post%")->_or_()
                ->whereLike('CONCAT(person.lname,", ",person.fname)', "%$post%")->_or_()
                ->whereLike('person.fname', "%$post%")->_or_()
                ->whereLike('person.lname', "%$post%")->_or_()
                ->whereLike('person.uname', "%$post%")->_or_()
                ->whereLike('person.personID', "%$post%")->_or_()
                ->whereLike('person.altID', "%$post%");
            $q = $search->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            $staff = _file_get_contents($json_url . 'staff/staffID/' . $q[0]['personID'] . '/?key=' . get_option('api_key'));
            $s_decode = json_decode($staff, true);
            $appl = _file_get_contents($json_url . 'application/personID/' . $q[0]['personID'] . '/?key=' . get_option('api_key'));
            $a_decode = json_decode($appl, true);
        }

        $app->view->display('person/index', [
            'title' => 'Name and Address',
            'cssArray' => $css,
            'jsArray' => $js,
            'search' => $q,
            'staff' => $s_decode,
            'appl' => $a_decode
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_person_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        if ($app->req->isPost()) {
            $nae = $app->db->person();
            foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                $nae->$k = $v;
            }
            $nae->where('personID = ?', $id);
            if ($nae->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Person (NAE)', get_name($id), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);
        $staff = _file_get_contents($json_url . 'staff/staffID/' . $id . '/?key=' . get_option('api_key'));
        $s_decode = json_decode($staff, true);
        $appl = _file_get_contents($json_url . 'application/personID/' . $id . '/?key=' . get_option('api_key'));
        $a_decode = json_decode($appl, true);

        $addr = $app->db->address()
            ->where('addressType = "P"')->_and_()
            ->where('endDate = "0000-00-00"')->_and_()
            ->where('addressStatus = "C"')->_and_()
            ->where('personID = ?', $id);

        $q = $addr->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

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
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/view', [
                'title' => get_name($decode[0]['personID']),
                'cssArray' => $css,
                'jsArray' => $js,
                'nae' => $decode,
                'addr' => $q,
                'staff' => $s_decode,
                'appl' => $a_decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/add/', function() {
        if (!hasPermission('add_person')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/add/', function () use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow, $email) {

        if ($app->req->isPost()) {
            $dob = str_replace('-', '', $_POST['dob']);
            $ssn = str_replace('-', '', $_POST['ssn']);

            if ($_POST['ssn'] > 0) {
                $password = et_hash_password((int) $ssn);
            } elseif (!empty($_POST['dob'])) {
                $password = et_hash_password((int) $dob);
            } else {
                $password = et_hash_password('myaccount');
            }

            $nae = $app->db->person();
            $nae->uname = $_POST['uname'];
            $nae->altID = $_POST['altID'];
            $nae->personType = $_POST['personType'];
            $nae->prefix = $_POST['prefix'];
            $nae->fname = $_POST['fname'];
            $nae->lname = $_POST['lname'];
            $nae->mname = $_POST['mname'];
            $nae->email = $_POST['email'];
            $nae->ssn = $_POST['ssn'];
            $nae->veteran = $_POST['veteran'];
            $nae->ethnicity = $_POST['ethnicity'];
            $nae->dob = $_POST['dob'];
            $nae->gender = $_POST['gender'];
            $nae->emergency_contact = $_POST['emergency_contact'];
            $nae->emergency_contact_phone = $_POST['emergency_contact_phone'];
            $nae->status = "A";
            $nae->approvedBy = get_persondata('personID');
            $nae->approvedDate = $app->db->NOW();
            $nae->password = $password;
            if ($nae->save()) {
                $ID = $nae->lastInsertId();
				
				$role = $app->db->person_roles();
				$role->personID = $ID;
				$role->roleID = $_POST['roleID'];
				$role->addDate = $app->db->NOW();
				$role->save();
				
                $addr = $app->db->address();
                $addr->personID = $ID;
                $addr->address1 = $_POST['address1'];
                $addr->address2 = $_POST['address2'];
                $addr->city = $_POST['city'];
                $addr->state = $_POST['state'];
                $addr->zip = $_POST['zip'];
                $addr->country = $_POST['country'];
                $addr->addressType = "P";
                $addr->addressStatus = "C";
                $addr->startDate = $addr->NOW();
                $addr->addDate = $addr->NOW();
                $addr->addedBy = get_persondata('personID');
                $addr->phone1 = $_POST['phone'];
                $addr->email1 = $_POST['email'];

                if (isset($_POST['sendemail']) && $_POST['sendemail'] == 'send') {
                    if ($_POST['ssn'] > 0) {
                        $pass = (int) $ssn;
                    } elseif (!empty($_POST['dob'])) {
                        $pass = (int) $dob;
                    } else {
                        $pass = 'myaccount';
                    }
                    $host = strtolower($_SERVER['SERVER_NAME']);
                    $site = _t('myeduTrac :: ') . get_option('institution_name');
                    $message = get_option('person_login_details');
                    $message = str_replace('#uname#', $_POST['uname'], $message);
                    $message = str_replace('#fname#', $_POST['fname'], $message);
                    $message = str_replace('#lname#', $_POST['lname'], $message);
                    $message = str_replace('#name#', $_POST['lname'] . ', ' . $_POST['fname'], $message);
                    $message = str_replace('#id#', $ID, $message);
                    $message = str_replace('#altID#', $_POST['altID'], $message);
                    $message = str_replace('#password#', $pass, $message);
                    $message = str_replace('#url#', url('/'), $message);
                    $message = str_replace('#helpdesk#', get_option('help_desk'), $message);
                    $message = str_replace('#instname#', get_option('institution_name'), $message);
                    $message = str_replace('#mailaddr#', get_option('mailing_address'), $message);

                    $headers = "From: $site <dont-reply@$host>\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $email->et_mail($_POST['email'], _t("myeduTrac Login Details"), $message, $headers);
                }
                if ($addr->save()) {
                    $logger->setLog('New Record', 'Name and Address', get_name($ID), get_persondata('uname'));
                    $app->flash('success_message', $flashNow->notice(200));
                    redirect(url('/nae/') . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        $app->view->display('person/add', [
            'title' => 'New Name and Address',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/adsu/(\d+)/', function() {
        if (!hasPermission('access_person_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->get('/adsu/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        
        $staff = _file_get_contents($json_url . 'staff/staffID/' . $id . '/?key=' . get_option('api_key'));
        $s_decode = json_decode($staff, true);

        $adsu = $app->db->person()
            ->setTableAlias('a')
            ->select('a.personID,a.fname,a.lname,a.mname')
            ->select('b.addressID,b.address1,b.address2,b.city')
            ->select('b.state,b.zip,b.addressType,b.addressStatus')
            ->_join('address', 'a.personID = b.personID', 'b')
            ->where('a.personID = ?', $id)->_and_()
            ->where('b.personID <> "NULL"');

        $q = $adsu->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($q == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($q[0]['personID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/adsu', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'nae' => $q,
                'staff' => $s_decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addr-form/(\d+)/', function() {
        if (!hasPermission('add_address')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/addr-form/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);
        
        $staff = _file_get_contents($json_url . 'staff/staffID/' . $id . '/?key=' . get_option('api_key'));
        $s_decode = json_decode($staff, true);

        if ($app->req->isPost()) {
            $addr = $app->db->address();
            $addr->personID = $decode[0]['personID'];
            $addr->address1 = $_POST['address1'];
            $addr->address2 = $_POST['address2'];
            $addr->city = $_POST['city'];
            $addr->state = $_POST['state'];
            $addr->zip = $_POST['zip'];
            $addr->country = $_POST['country'];
            $addr->addressType = $_POST['addressType'];
            $addr->startDate = $_POST['startDate'];
            $addr->endDate = $_POST['endDate'];
            $addr->addressStatus = $_POST['addressStatus'];
            $addr->phone1 = $_POST['phone1'];
            $addr->phone2 = $_POST['phone2'];
            $addr->ext1 = $_POST['ext1'];
            $addr->ext2 = $_POST['ext2'];
            $addr->phoneType1 = $_POST['phoneType1'];
            $addr->phoneType2 = $_POST['phoneType2'];
            $addr->email2 = $_POST['email2'];
            $addr->addDate = $addr->NOW();
            $addr->addedBy = get_persondata('personID');

            if ($addr->save()) {
                $ID = $addr->lastInsertId();
                $logger->setLog('New Record', 'Address', get_name($decode[0]['personID']), get_persondata('uname'));
                $app->flash('success_message', $flashNow->notice(200));
                redirect(url('/nae/addr/') . $ID . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

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
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/addr-form', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'nae' => $decode,
                'staff' => $s_decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addr/(\d+)/', function() {
        if (!hasPermission('access_person_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/addr/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {

        $json_a = _file_get_contents($json_url . 'address/addressID/' . $id . '/?key=' . get_option('api_key'));
        $a_decode = json_decode($json_a, true);

        $json_p = _file_get_contents($json_url . 'person/personID/' . $a_decode[0]['personID'] . '/?key=' . get_option('api_key'));
        $p_decode = json_decode($json_p, true);
        
        $staff = _file_get_contents($json_url . 'staff/staffID/' . $id . '/?key=' . get_option('api_key'));
        $s_decode = json_decode($staff, true);

        if ($app->req->isPost()) {
            $addr = $app->db->address();
            foreach ($_POST as $k => $v) {
                $addr->$k = $v;
            }
            $addr->where('addressID = ?', $id);
            if ($addr->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Address', get_name($a_decode[0]['personID']), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($a_decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($a_decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($a_decode[0]['addressID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/addr', [
                'title' => get_name($a_decode[0]['personID']),
                'cssArray' => $css,
                'jsArray' => $js,
                'addr' => $a_decode,
                'nae' => $p_decode,
                'staff' => $s_decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/role/(\d+)/', function() {
        if (!hasPermission('access_user_role_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/role/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);
        
        $staff = _file_get_contents($json_url . 'staff/staffID/' . $id . '/?key=' . get_option('api_key'));
        $s_decode = json_decode($staff, true);

        if ($app->req->isPost()) {
            foreach ($_POST as $k => $v) {
                if (substr($k, 0, 5) == "role_") {
                    $roleID = str_replace("role_", "", $k);
                    if ($v == '0' || $v == 'x') {
                        $strSQL = sprintf("DELETE FROM `person_roles` WHERE `personID` = %u AND `roleID` = %u", $id, $roleID);
                    } else {
                        $strSQL = sprintf("REPLACE INTO `person_roles` SET `personID` = %u, `roleID` = %u, `addDate` = '%s'", $id, $roleID, $app->db->NOW());
                    }
                    $q = $app->db->query($strSQL);
                }
            }
            if ($q) {
                $app->flash('success_message', $flashNow->notice(200));
                redirect(url('/nae/role/') . $id . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

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
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/role', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'nae' => $decode,
                'staff' => $s_decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/perms/(\d+)/', function() {
        if (!hasPermission('access_user_permission_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/perms/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);
        
        $staff = _file_get_contents($json_url . 'staff/staffID/' . $id . '/?key=' . get_option('api_key'));
        $s_decode = json_decode($staff, true);

        if ($app->req->isPost()) {
            if (count($_POST['permission']) > 0) {
                $q = $app->db->query(sprintf("REPLACE INTO person_perms SET personID = %u, permission = '%s'", $id, maybe_serialize($_POST['permission'])));
            } else {
                $q = $app->db->query(sprintf("DELETE FROM person_perms WHERE personID = %u", $id));
            }
            if ($q) {
                $app->flash('success_message', $flashNow->notice(200));
                redirect(url('/nae/perms/') . $id . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

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
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/perms', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'nae' => $decode,
                'staff' => $s_decode
                ]
            );
        }
    });

    $app->match('GET|POST', '/usernameCheck/', function () use($app) {
        $uname = $app->db->person()->where('uname', $_POST['uname']);
        $q = $uname->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        foreach ($q as $v) :
            if ($v['uname'] == $_POST['uname']) :
                echo '1';
            endif;
        endforeach;
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/resetPassword/(\d+)/', function() {
        if (!hasPermission('reset_person_password')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->get('/resetPassword/(\d+)/', function ($id) use($app, $logger, $flashNow, $email) {

        $person = $app->db->person()
            ->select('uname,email,fname,lname,dob,ssn')
            ->where('personID = ?', $id);
        $q1 = $person->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $a = [];
        foreach ($q1 as $r1) {
            $a[] = $r1;
        }
        $dob = str_replace('-', '', $r1['dob']);
        if ($r1['ssn'] > 0) {
            $pass = $r1['ssn'];
        } elseif ($r1['dob'] != '0000-00-00') {
            $pass = $dob;
        } else {
            $pass = 'myaccount';
        }
        $from = get_option('institution_name');
        $fromEmail = get_option('system_email');
        $url = url('/');
        $host = $app->req->server['HTTP_HOST'];
        $helpDesk = get_option('help_desk');
        $body = get_option('reset_password_text');
        $body = str_replace('#instname#', get_option('institution_name'), $body);
        $body = str_replace('#mailaddr#', get_option('mailing_address'), $body);
        $body = str_replace('#url#', $url, $body);
        $body = str_replace('#helpdesk#', $helpDesk, $body);
        $body = str_replace('#adminemail#', $fromEmail, $body);
        $body = str_replace('#uname#', _h($r1['uname']), $body);
        $body = str_replace('#email#', _h($r1['email']), $body);
        $body = str_replace('#name#', _h($r1['lname']) . ', ' . _h($r1['fname']), $body);
        $body = str_replace('#fname#', _h($r1['fname']), $body);
        $body = str_replace('#lname#', _h($r1['lname']), $body);
        $body = str_replace('#password#', $pass, $body);
        $headers = "From: $from <auto-reply@$host>\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $password = et_hash_password($pass);
        $q2 = $app->db->person();
        $q2->password = $password;
        $q2->where('personID = ?', $id);
        if ($q2->update()) {
            $app->flash('success_message', 'The password has been reset and an email has been sent to this user.');
            $email->et_mail($r1['email'], "Reset Password", $body, $headers);
            $logger->setLog('Update Record', 'Reset Password', get_name($id), get_persondata('uname'));
        } else {
            $app->flash('error_message', $flashNow->notice(409));
        }
        redirect($app->req->server['HTTP_REFERER']);
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Application Router
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
    'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/multiselect/assets/lib/js/jquery.multi-select.js?v=v2.1.0',
    'components/modules/admin/forms/elements/multiselect/assets/custom/js/multiselect.init.js?v=v2.1.0'
];

$json_url = url('/api/');

$logger = new \app\src\Log();
$email = new \app\src\Email();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();

$app->group('/appl', function () use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow, $email) {

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/', function() {
        if (!hasPermission('access_application_screen')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->match('GET|POST', '/', function () use($app, $css, $js, $json_url) {

        if ($app->req->isPost()) {
            $post = $_POST['appl'];
            $appl = $app->db->application()
                ->setTableAlias('a')
                ->select('a.applID,a.personID,b.termName,c.fname,c.lname,c.uname,c.email')
                ->_join('term', 'a.startTerm = b.termCode', 'b')
                ->_join('person', 'a.personID = c.personID', 'c')
                ->whereLike('CONCAT(c.fname," ",c.lname)', "%$post%")->_or_()
                ->whereLike('CONCAT(c.lname," ",c.fname)', "%$post%")->_or_()
                ->whereLike('CONCAT(c.lname,", ",c.fname)', "%$post%")->_or_()
                ->whereLike('c.fname', "%$post%")->_or_()
                ->whereLike('c.lname', "%$post%")->_or_()
                ->whereLike('c.uname', "%$post%")->_or_()
                ->whereLike('a.personID', "%$post%");
            $q = $appl->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            $json = _file_get_contents($json_url . 'student/stuID/' . $q[0]['personID'] . '/?key=' . get_option('api_key'));
            $decode = json_decode($json, true);
        }

        $app->view->display('application/index', [
            'title' => 'Application Search',
            'cssArray' => $css,
            'jsArray' => $js,
            'search' => $q,
            'appl' => $decode
            ]
        );
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_application_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->get('/(\d+)/', function ($id) use($app, $css, $js) {

        $appl = $app->db->application()
            ->setTableAlias('a')
            ->select('a.*,b.fname,b.mname,b.lname,b.dob,b.uname')
            ->select('b.email,b.gender')
            ->_join('person', 'a.personID = b.personID', 'b')
            ->where('a.applID = ?', $id);
        $q1 = $appl->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $addr = $app->db->address()
            ->setTableAlias('a')
            ->_join('application', 'a.personID = b.personID', 'b')
            ->where('b.applID = ?', $id)->_and_()
            ->where('a.addressType = "P"');
        $q2 = $addr->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $inst = $app->db->institution_attended()
            ->setTableAlias('a')
            ->_join('application', 'a.personID = b.personID', 'b')
            ->where('b.applID = ?', $id);
        $q3 = $inst->find(function($data) {
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
        if ($q1 == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q1) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($q1) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('application/view', [
                'title' => get_name($q1[0]['personID']),
                'cssArray' => $css,
                'jsArray' => $js,
                'appl' => $q1,
                'addr' => $q2,
                'inst' => $q3
                ]
            );
        }
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/editAppl/(\d+)/', function() {
        if (!hasPermission('create_application')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->post('/editAppl/(\d+)/', function ($id) use($app, $logger, $flashNow, $email) {
        $appl = $app->db->application();
        $appl->acadProgCode = $_POST['acadProgCode'];
        $appl->startTerm = $_POST['startTerm'];
        $appl->PSAT_Verbal = $_POST['PSAT_Verbal'];
        $appl->PSAT_Math = $_POST['PSAT_Math'];
        $appl->SAT_Verbal = $_POST['SAT_Verbal'];
        $appl->SAT_Math = $_POST['SAT_Math'];
        $appl->ACT_English = $_POST['ACT_English'];
        $appl->ACT_Math = $_POST['ACT_Math'];
        $appl->applDate = $_POST['applDate'];
        $appl->appl_comments = $_POST['appl_comments'];
        $appl->staff_comments = $_POST['staff_comments'];
        $appl->applStatus = $_POST['applStatus'];
        $appl->acadProgCode = $_POST['acadProgCode'];
        $appl->acadProgCode = $_POST['acadProgCode'];
        $appl->where('applID = ?', $_POST['applID']);

        if ($appl->update()) {
            $app->flash('success_message', $flashNow->notice(200));
            $logger->setLog('Update Record', 'Application', get_name($_POST['personID']), get_persondata('uname'));
        } else {
            $app->flash('error_message', $flashNow->notice(409));
        }

        $person = $app->db->person()->select('uname')->where('personID = ?', $_POST['personID']);
        $q = $person->find(function($data) {
            $array = [];
            foreach($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $uname = $app->db->person();
        $uname->uname = $_POST['uname'];
        $uname->where('personID = ?', $_POST['personID']);
        if ($q[0]['uname'] !== $_POST['uname']) {
            if ($uname->update()) {
                $host = strtolower($_SERVER['SERVER_NAME']);
                $site = get_option('institution_name');
                
                $message = get_option('update_username');
                $message = str_replace('#uname#', getUserValue($_POST['personID'], 'uname'), $message);
                $message = str_replace('#fname#', getUserValue($_POST['personID'], 'fname'), $message);
                $message = str_replace('#lname#', getUserValue($_POST['personID'], 'lname'), $message);
                $message = str_replace('#name#', get_name($_POST['personID']), $message);
                $message = str_replace('#id#', $_POST['personID'], $message);
                $message = str_replace('#altID#', getUserValue($_POST['personID'], 'altID'), $message);
                $message = str_replace('#url#', url('/'), $message);
                $message = str_replace('#helpdesk#', get_option('help_desk'), $message);
                $message = str_replace('#instname#', get_option('institution_name'), $message);
                $message = str_replace('#mailaddr#', get_option('mailing_address'), $message);
                
                $headers = "From: $site <dont-reply@$host>\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $email->et_mail(getUserValue($_POST['personID'], 'email'), _t("myeduTrac Username Change"), $message, $headers);
                    
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Application', get_name($_POST['personID']), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
        }

        $size = count($_POST['fice_ceeb']);
        $i = 0;
        while ($i < $size) {
            $inst = $app->db->institution_attended();
            $inst->fice_ceeb = $_POST['fice_ceeb'][$i];
            $inst->fromDate = $_POST['fromDate'][$i];
            $inst->toDate = $_POST['toDate'][$i];
            $inst->GPA = $_POST['GPA'][$i];
            $inst->major = $_POST['major'][$i];
            $inst->degree_awarded = $_POST['degree_awarded'][$i];
            $inst->degree_conferred_date = $_POST['degree_conferred_date'][$i];
            $inst->where('instAttID = ?', $_POST['instAttID'][$i])->_and_()
                ->where('personID = ?', $_POST['personID']);
            if ($inst->update()) {
                $app->flash('success_message', $flashNow->notice(200));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            ++$i;
        }

        redirect(url('/appl/') . $id . '/');
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/add/(\d+)/', function() {
        if (!hasPermission('create_application')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            $appl = $app->db->application();
            $appl->acadProgCode = _trim($_POST['acadProgCode']);
            $appl->startTerm = $_POST['startTerm'];
            $appl->PSAT_Verbal = $_POST['PSAT_Verbal'];
            $appl->PSAT_Math = $_POST['PSAT_Math'];
            $appl->SAT_Verbal = $_POST['SAT_Verbal'];
            $appl->SAT_Math = $_POST['SAT_Math'];
            $appl->ACT_English = $_POST['ACT_English'];
            $appl->ACT_MATH = $_POST['ACT_Math'];
            $appl->personID = $id;
            $appl->addDate = $app->db->NOW();
            $appl->applDate = $_POST['applDate'];
            $appl->addedBy = get_persondata('personID');
            $appl->admitStatus = $_POST['admitStatus'];
            if ($appl->save()) {
                $ID = $appl->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Application', get_name($id), get_persondata('uname'));
                redirect(url('/appl/') . $ID . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        $person = $app->db->person()
            ->where('personID = ?', $id);
        $q1 = $person->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $address = $app->db->address()
            ->where('personID = ?', $id)->_and_()
            ->where('addressType = "P"')->_and_()
            ->where('addressStatus = "C"')->_and_()
            ->whereLte('endDate', '0000-00-00');
        $q2 = $address->find(function($data) {
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
        if ($q1 == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q1) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($q1) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('application/add', [
                'title' => 'Create Application',
                'cssArray' => $css,
                'jsArray' => $js,
                'person' => $q1,
                'address' => $q2
                ]
            );
        }
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/inst-attended/', function() {
        if (!hasPermission('access_application_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/inst-attended/', function () use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            $inst = $app->db->institution_attended();
            $inst->fice_ceeb = _trim((int)$_POST['fice_ceeb']);
            $inst->fromDate = $_POST['fromDate'];
            $inst->toDate = $_POST['toDate'];
            $inst->GPA = $_POST['GPA'];
            $inst->personID = $_POST['personID'];
            $inst->major = $_POST['major'];
            $inst->degree_awarded = $_POST['degree_awarded'];
            $inst->degree_conferred_date = $_POST['degree_conferred_date'];
            $inst->addDate = $inst->NOW();
            $inst->addedBy = get_persondata('personID');
            if ($inst->save()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Institution Attended', get_name($_POST['personID']), get_persondata('uname'));
                redirect(url('/appl/') . $_POST['personID'] . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        $app->view->display('application/inst-attended', [
            'title' => 'Institutions Attended',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/inst(.*)', function() {
        if (!hasPermission('access_application_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/inst/', function () use($app, $css, $js) {

        if ($app->req->isPost()) {
            $post = $_POST['inst'];
            $inst = $app->db->institution()
                ->whereLike('instName', "%$post%")->_or_()
                ->whereLike('fice_ceeb', "%$post%");
            $q = $inst->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        }

        $app->view->display('application/inst', [
            'title' => 'Institution Search',
            'cssArray' => $css,
            'jsArray' => $js,
            'search' => $q
            ]
        );
    });

    $app->match('GET|POST', '/inst/add/', function () use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            $inst = $app->db->institution();
            $inst->fice_ceeb = _trim((int)$_POST['fice_ceeb']);
            $inst->instType = $_POST['instType'];
            $inst->instName = $_POST['instName'];
            $inst->city = $_POST['city'];
            $inst->state = $_POST['state'];
            $inst->country = $_POST['country'];
            if ($inst->save()) {
                $ID = $inst->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Institution', $_POST['instName'], get_persondata('uname'));
                redirect(url('/appl/inst/') . $ID . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        $app->view->display('application/add-inst', [
            'title' => 'Add Institution',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    $app->match('GET|POST', '/inst/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        if ($app->req->isPost()) {
            $inst = $app->db->institution();
            foreach ($_POST as $k => $v) {
                $inst->$k = $v;
            }
            $inst->where('institutionID = ?', $id);
            if ($inst->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Institution', _filter_input_string(INPUT_POST, 'instName'), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $json = _file_get_contents($json_url . 'institution/institutionID/' . (int) $id . '/?key=' . get_option('api_key'));
        $inst = json_decode($json, true);

        $app->view->display('application/view-inst', [
            'title' => $inst[0]['instName'],
            'cssArray' => $css,
            'jsArray' => $js,
            'inst' => $inst
            ]
        );
    });

    $app->get('/applications/', function () use($app, $json_url) {

        $css = [ 'css/admin/module.admin.page.alt.form_elements.min.css', 'css/admin/module.admin.page.alt.tables.min.css'];
        $js = [
            'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
            'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
            'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
            'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
            'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
            'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
            'components/modules/admin/forms/elements/bootstrap-timepicker/assets/lib/js/bootstrap-timepicker.js?v=v2.1.0',
            'components/modules/admin/forms/elements/bootstrap-timepicker/assets/custom/js/bootstrap-timepicker.init.js?v=v2.1.0'
        ];

        $json_a = _file_get_contents($json_url . 'application/personID/' . (int) get_persondata('personID') . '/?key=' . get_option('api_key'));
        $appl = json_decode($json_a, true);

        $app->view->display('application/appls', [
            'title' => 'My Applications',
            'cssArray' => $css,
            'jsArray' => $js,
            'appls' => $appl
            ]
        );
    });

    $app->post('/applicantLookup/', function() use($json_url) {
        $json_a = _file_get_contents($json_url . 'person/personID/' . (int) $_POST['personID'] . '/?key=' . get_option('api_key'));
        $appl = json_decode($json_a, true);

        $json = [ 'input#person' => $appl[0]['lname'] . ', ' . $appl[0]['fname']];

        echo json_encode($json);
    });

    $app->get('/deleteInstAttend/(\d+)/', function($id) use($app, $flashNow) {
        $inst = $app->db->institution_attended()->where('instAttID = ?', $id);
        if ($inst->delete()) {
            $app->flash('success_message', $flashNow->notice(200));
        } else {
            $app->flash('error_message', $flashNow->notice(409));
        }
        redirect($app->req->server['HTTP_REFERER']);
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

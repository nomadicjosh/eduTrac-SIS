<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Before router middleware checks to see
 * if the user is logged in.
 */
$app->before('GET|POST|PUT|DELETE|PATCH|HEAD', '/staff(.*)', function() use ($app) {
    if (!isUserLoggedIn()) {
        redirect(url('/login/'));
    }
    /**
     * If user is logged in and the lockscreen cookie is set, 
     * redirect user to the lock screen until he/she enters 
     * his/her password to gain access.
     */
    if (isset($_COOKIE['SCREENLOCK'])) {
        redirect(url('/') . 'lock/');
    }
});

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
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
];

$json_url = url('/api/');

$logger = new \app\src\Log();
$cache = new \app\src\Cache();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();

$app->group('/staff', function () use($app, $css, $js, $json_url, $dbcache, $logger, $flashNow) {

    $app->match('GET|POST', '/', function () use($app, $css, $js) {

        /**
         * Before route middleware check.
         */
        $app->before('GET|POST', '/staff/', function() use ($app) {
            if (!hasPermission('access_staff_screen')) {
                redirect(url('/dashboard/'));
            }
        });

        if ($app->req->isPost()) {
            $post = $_POST['staff'];
            $search = $app->db->staff()
                ->setTableAlias('a')
                ->select('a.staffID,b.lname,b.fname,b.email')
                ->_join('person', 'a.staffID = b.personID', 'b')
                ->whereLike('CONCAT(b.fname," ",b.lname)', "%$post%")->_or_()
                ->whereLike('CONCAT(b.lname," ",b.fname)', "%$post%")->_or_()
                ->whereLike('CONCAT(b.lname,", ",b.fname)', "%$post%")->_or_()
                ->whereLike('a.staffID', "%$post%");
            $q = $search->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        }

        $app->view->display('staff/index', [
            'title' => 'Staff Search',
            'cssArray' => $css,
            'jsArray' => $js,
            'search' => $q
            ]
        );
    });

    /**
     * Before route middleware check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_staff_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$staff = $app->db->staff();
    		foreach(_filter_input_array(INPUT_POST) as $k => $v) {
    			$staff->$k = $v;
    		}
    		$staff->where('staffID = ?', $id);
    		if($staff->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Staff', get_name($id), get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
        
        $json = _file_get_contents($json_url . 'staff/staffID/' . $id . '/?key=' . $app->hook->{'get_option'}('api_key'));
        $decode = json_decode($json, true);

        $addr = $app->db->address()
            ->setTableAlias('a')
            ->select("*,person.email")
            ->_join('staff', 'a.personID = b.staffID', 'b')
            ->_join('person', 'a.personID = person.personID')
            ->where('a.addressType = "P"')->_and_()
            ->where('a.addressStatus = "C"')->_and_()
            ->where('a.personID = ?', $id);
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
         */ elseif (count($decode[0]['staffID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('staff/view', [
                'title' => get_name($decode[0]['staffID']),
                'cssArray' => $css,
                'jsArray' => $js,
                'staff' => $decode,
                'addr' => $q
                ]
            );
        }
    });

    /**
     * Before route middleware check.
     */
    $app->before('GET|POST', '/add/(\d+)/', function($id) use ($app) {
        if (!hasPermission('create_staff_record')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app, $css, $js, $json_url, $dbcache, $logger, $flashNow) {

        $json_p = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . $app->hook->{'get_option'}('api_key'));
        $p_decode = json_decode($json_p, true);

        $json_s = _file_get_contents($json_url . 'staff/staffID/' . $id . '/?key=' . $app->hook->{'get_option'}('api_key'));
        $s_decode = json_decode($json_s, true);

        if ($app->req->isPost()) {
            $staff = $app->db->staff();
            $staff->staffID = $id;
            $staff->schoolCode = $_POST['schoolCode'];
            $staff->buildingCode = $_POST['buildingCode'];
            $staff->officeCode = $_POST['officeCode'];
            $staff->office_phone = $_POST['office_phone'];
            $staff->deptCode = $_POST['deptCode'];
            $staff->status = $_POST['status'];
            $staff->addDate = $staff->NOW();
            $staff->approvedBy = get_persondata('personID');

            $meta = $app->db->staff_meta();
            $meta->jobStatusCode = $_POST['jobStatusCode'];
            $meta->jobID = $_POST['jobID'];
            $meta->staffID = $id;
            $meta->supervisorID = $_POST['supervisorID'];
            $meta->staffType = $_POST['staffType'];
            $meta->hireDate = $_POST['hireDate'];
            $meta->startDate = $_POST['startDate'];
            $meta->endDate = $_POST['endDate'];
            $meta->addDate = $meta->NOW();
            $meta->approvedBy = get_persondata('personID');
            if ($staff->save() && $meta->save()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Staff Member', get_name($id), get_persondata('uname'));
                redirect(url('/staff/') . $id);
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($p_decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($p_decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($p_decode[0]['personID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If staffID already exists, then redirect
         * the user to the staff record.
         */ elseif (count($s_decode[0]['staffID']) > 0) {

            redirect(url('/staff/') . $s_decode[0]['staffID'] . '/');
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('staff/add', [
                'title' => get_name($p_decode[0]['personID']),
                'cssArray' => $css,
                'jsArray' => $js,
                'person' => $p_decode
                ]
            );
        }
    });
});

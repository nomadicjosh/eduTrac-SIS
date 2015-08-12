<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Human Resources Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

/**
 * Before router middleware checks to see
 * if the user is logged in.
 */
$app->before('GET|POST|PUT|DELETE|PATCH|HEAD', '/hr(.*)', function() {
    if (!hasPermission('access_human_resources')) {
        redirect(url('/') . 'dashboard/');
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
    'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
];

$logger = new \app\src\Log();
$cache = new \app\src\Cache();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();

$app->group('/hr', function () use($app, $css, $js, $dbcache, $logger, $flashNow) {

    $app->match('GET|POST', '/', function () use($app, $css, $js) {
        if ($app->req->isPost()) {
            $staff = $_POST['employee'];
            $hr = $app->db->staff()
                ->select('staff.staffID,staff.office_phone,b.email,c.deptName')
                ->_join('person', 'staff.staffID = b.personID', 'b')
                ->_join('department', 'staff.deptCode = c.deptCode', 'c')
                ->whereLike('CONCAT(b.fname," ",b.lname)', "%$staff%")->_or_()
                ->whereLike('CONCAT(b.lname," ",b.fname)', "%$staff%")->_or_()
                ->whereLike('CONCAT(b.lname,", ",b.fname)', "%$staff%")->_or_()
                ->whereLike('staff.staffID', "%$staff%");

            $q = $hr->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        }
        $app->view->display('hr/index', [
            'title' => 'Human Resources',
            'cssArray' => $css,
            'jsArray' => $js,
            'search' => $q
            ]
        );
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $dbcache, $logger, $flashNow) {
        if ($app->req->isPost()) {
            $staff = $app->db->staff();
            $staff->schoolCode = $_POST['schoolCode'];
            $staff->buildingCode = $_POST['buildingCode'];
            $staff->officeCode = $_POST['officeCode'];
            $staff->office_phone = $_POST['office_phone'];
            $staff->deptCode = $_POST['deptCode'];
            $staff->status = $_POST['status'];
            $staff->where('staffID = ?', $id);

            $smeta = $app->db->stafF_meta();
            $smeta->jobStatusCode = $_POST['jobStatusCode'];
            $smeta->jobID = $_POST['jobID'];
            $smeta->supervisorID = $_POST['supervisorID'];
            $smeta->staffType = $_POST['staffType'];
            $smeta->hireDate = $_POST['hireDate'];
            $smeta->startDate = $_POST['startDate'];
            $smeta->endDate = $_POST['endDate'];
            $smeta->where('sMetaID = ?', $_POST['sMetaID'])->_and_()->where('staffID = ?', $id);

            if ($staff->update() || $smeta->update()) {
                // Delete db cache if data was updated successfully
                $dbcache->clearCache("staff-$id");
                $app->flash('success_message', $flashNow->notice(200));
            } else {
                $app->flash('error_message', $flashNow->notice(204));
            }

            redirect(url('/hr/') . $id . '/');
        }

        $empl = $app->db->staff()
            ->select('staff.*,b.sMetaID,b.jobStatusCode,b.jobID')
            ->select('b.supervisorID,b.staffType,b.hireDate')
            ->select('b.startDate,b.endDate,c.title')
            ->select('c.hourly_wage,c.weekly_hours')
            ->select('SUM(c.hourly_wage*c.weekly_hours*4) AS Monthly,d.prefix')
            ->_join('staff_meta', 'staff.staffID = b.staffID', 'b')
            ->_join('job', 'b.jobID = c.ID', 'c')
            ->_join('person', 'staff.staffID = d.personID', 'd')
            ->where('staff.staffID = ?', $id)->_and_()
            ->where('b.hireDate = (SELECT MAX(hireDate) FROM staff_meta WHERE staffID = ?)', $id);

        $q = $empl->find(function($data) {
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
         */ elseif (count($q[0]['staffID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {
            $app->view->display('hr/view', [
                'title' => 'View Staff Member',
                'cssArray' => $css,
                'jsArray' => $js,
                'staff' => $q
                ]
            );
        }
    });

    $app->match('GET|POST', '/grades/', function () use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            if (isset($_POST['addDate'])) {
                $pg = $app->db->pay_grade();
                foreach ($_POST as $k => $v) {
                    $pg->$k = $v;
                }
                if ($pg->save()) {
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Pay Grade', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
            } else {
                $pg = $app->db->pay_grade();
                foreach ($_POST as $k => $v) {
                    $pg->$k = $v;
                }
                $pg->where('ID = ?', _filter_input_int(INPUT_POST, 'ID'));
                if ($pg->update()) {
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Pay Grade', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $pg = $app->db->pay_grade();
        $q = $pg->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('hr/grades', [
            'title' => 'Pay Grades',
            'cssArray' => $css,
            'jsArray' => $js,
            'grades' => $q
            ]
        );
    });

    $app->match('GET|POST', '/jobs/', function () use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            if (isset($_POST['addDate'])) {
                $job = $app->db->job();
                foreach ($_POST as $k => $v) {
                    $job->$k = $v;
                }
                if ($job->save()) {
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Job', _filter_input_string(INPUT_POST, 'title'), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
            } else {
                $job = $app->db->job();
                foreach ($_POST as $k => $v) {
                    $job->$k = $v;
                }
                $job->where('ID = ?', _filter_input_int(INPUT_POST, 'ID'));
                if ($job->update()) {
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Job', _filter_input_string(INPUT_POST, 'title'), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $jobs = $app->db->job()->select('job.*,b.grade')->_join('pay_grade', 'job.pay_grade = b.ID', 'b');
        $q = $jobs->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('hr/jobs', [
            'title' => 'Jobs',
            'cssArray' => $css,
            'jsArray' => $js,
            'jobs' => $q
            ]
        );
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if ($app->req->isPost()) {
            $position = $app->db->staff_meta();
            foreach ($_POST as $k => $v) {
                $position->$k = $v;
            }
            if ($position->save()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Job Position', get_name($id), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $staff = $app->db->staff()->where('staffID = ?', $id);
        $q = $staff->find(function($data) {
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
         */ elseif (count($q[0]['staffID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {
            $app->view->display('hr/add', [
                'title' => 'Add Position',
                'cssArray' => $css,
                'jsArray' => $js,
                'job' => $q
                ]
            );
        }
    });

    $app->match('GET|POST', '/positions/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            $position = $app->db->staff_meta();
            foreach ($_POST as $k => $v) {
                $position->$k = $v;
            }
            $position->where('sMetaID = ?', _filter_input_int(INPUT_POST, 'sMetaID'));
            if ($position->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Job Position', get_name($id), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $jobs = $app->db->staff_meta()
            ->select('staff_meta.*,b.title,b.hourly_wage')
            ->select('b.weekly_hours,c.grade')
            ->_join('job', 'staff_meta.jobID = b.ID', 'b')
            ->_join('pay_grade', 'b.pay_grade = c.ID ', 'c')
            ->where('staff_meta.staffID = ?', $id);
        $q = $jobs->find(function($data) {
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
         */ elseif (count($q[0]['staffID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {
            $app->view->display('hr/positions', [
                'title' => 'Current/Former Positions',
                'cssArray' => $css,
                'jsArray' => $js,
                'positions' => $q
                ]
            );
        }
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

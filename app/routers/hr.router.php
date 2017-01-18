<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;

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
$app->before('GET|POST', '/hr(.*)', function() {
    if (!hasPermission('access_human_resources')) {
        _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->group('/hr', function () use($app) {

    $app->match('GET|POST', '/', function () use($app) {
        if ($app->req->isPost()) {
            try {
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
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            }
        }

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('hr/index', [
            'title' => 'Human Resources',
            'search' => $q
            ]
        );
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $staff = $app->db->staff();
                $staff->schoolCode = $_POST['schoolCode'];
                $staff->buildingCode = $_POST['buildingCode'];
                $staff->officeCode = $_POST['officeCode'];
                $staff->office_phone = $_POST['office_phone'];
                $staff->deptCode = $_POST['deptCode'];
                $staff->status = $_POST['status'];
                $staff->where('staffID = ?', $id);

                $smeta = $app->db->staff_meta();
                $smeta->jobStatusCode = $_POST['jobStatusCode'];
                $smeta->jobID = $_POST['jobID'];
                $smeta->supervisorID = $_POST['supervisorID'];
                $smeta->staffType = $_POST['staffType'];
                $smeta->hireDate = $_POST['hireDate'];
                $smeta->startDate = $_POST['startDate'];
                $smeta->endDate = $_POST['endDate'];
                $smeta->where('sMetaID = ?', $_POST['sMetaID'])->_and_()->where('staffID = ?', $id);

                if ($staff->update() || $smeta->update()) {
                    _etsis_flash()->{'success'}(_etsis_flash()->notice(200), get_base_url() . 'hr' . '/' . $id . '/');
                } else {
                    _etsis_flash()->{'error'}(_etsis_flash()->notice(204));
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            }
        }

        try {
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
                ->where('b.hireDate = (SELECT MAX(hireDate) FROM staff_meta WHERE staffID = ?)', $id)
                ->findOne();
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($empl == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($empl) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($empl->staffID) <= 0) {

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
            etsis_register_script('datepicker');

            $app->view->display('hr/view', [
                'title' => 'View Staff Member',
                'staff' => $empl
                ]
            );
        }
    });

    $app->match('GET|POST', '/grades/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                if (isset($_POST['addDate'])) {
                    $pg = $app->db->pay_grade();
                    foreach ($_POST as $k => $v) {
                        $pg->$k = $v;
                    }
                    if ($pg->save()) {
                        etsis_logger_activity_log_write('New Record', 'Pay Grade', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
                        _etsis_flash()->{'success'}(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                    } else {
                        _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
                    }
                } else {
                    $pg = $app->db->pay_grade();
                    foreach ($_POST as $k => $v) {
                        $pg->$k = $v;
                    }
                    $pg->where('ID = ?', _filter_input_int(INPUT_POST, 'ID'));
                    if ($pg->update()) {
                        etsis_logger_activity_log_write('Update Record', 'Pay Grade', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
                        _etsis_flash()->{'success'}(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                    } else {
                        _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
                    }
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            }
        }

        try {
            $pg = $app->db->pay_grade();
            $q = $pg->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        }

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('hr/grades', [
            'title' => 'Pay Grades',
            'grades' => $q
            ]
        );
    });

    $app->match('GET|POST', '/jobs/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                if (isset($_POST['addDate'])) {
                    $job = $app->db->job();
                    foreach ($_POST as $k => $v) {
                        $job->$k = $v;
                    }
                    if ($job->save()) {
                        etsis_logger_activity_log_write('New Record', 'Job', _filter_input_string(INPUT_POST, 'title'), get_persondata('uname'));
                        _etsis_flash()->{'success'}(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                    } else {
                        _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
                    }
                } else {
                    $job = $app->db->job();
                    foreach ($_POST as $k => $v) {
                        $job->$k = $v;
                    }
                    $job->where('ID = ?', _filter_input_int(INPUT_POST, 'ID'));
                    if ($job->update()) {
                        etsis_logger_activity_log_write('Update Record', 'Job', _filter_input_string(INPUT_POST, 'title'), get_persondata('uname'));
                        _etsis_flash()->{'success'}(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                    } else {
                        _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
                    }
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            }
        }

        try {
            $jobs = $app->db->job()->select('job.*,b.grade')->_join('pay_grade', 'job.pay_grade = b.ID', 'b');
            $q = $jobs->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        }

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('hr/jobs', [
            'title' => 'Jobs',
            'jobs' => $q
            ]
        );
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $position = $app->db->staff_meta();
                foreach ($_POST as $k => $v) {
                    $position->$k = $v;
                }
                if ($position->save()) {
                    etsis_logger_activity_log_write('New Record', 'Job Position', get_name($id), get_persondata('uname'));
                    _etsis_flash()->{'success'}(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                } else {
                    _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            }
        }

        try {
            $staff = $app->db->staff()->where('staffID = ?', $id);
            $q = $staff->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        }


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

            etsis_register_style('form');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datepicker');

            $app->view->display('hr/add', [
                'title' => 'Add Position',
                'job' => $q
                ]
            );
        }
    });

    $app->match('GET|POST', '/positions/(\d+)/', function ($id) use($app) {

        if ($app->req->isPost()) {
            try {
                $position = $app->db->staff_meta();
                foreach ($_POST as $k => $v) {
                    $position->$k = $v;
                }
                $position->where('sMetaID = ?', _filter_input_int(INPUT_POST, 'sMetaID'));
                if ($position->update()) {
                    etsis_logger_activity_log_write('Update Record', 'Job Position', get_name($id), get_persondata('uname'));
                    _etsis_flash()->{'success'}(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                } else {
                    _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            }
        }

        try {
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
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        }

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

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datepicker');
            etsis_register_script('datatables');

            $app->view->display('hr/positions', [
                'title' => 'Current/Former Positions',
                'positions' => $q
                ]
            );
        }
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

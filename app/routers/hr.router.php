<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

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
    if(!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        exit();
    }
    if (!hasPermission('access_human_resources')) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        exit();
    }
});

$app->group('/hr', function () use($app) {

    $app->match('GET|POST', '/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $staff = $app->req->post['employee'];
                $hr = $app->db->staff()
                    ->select('staff.staffID,staff.office_phone,b.email,b.altID,c.deptName')
                    ->_join('person', 'staff.staffID = b.personID', 'b')
                    ->_join('department', 'staff.deptCode = c.deptCode', 'c')
                    ->whereLike('CONCAT(b.fname," ",b.lname)', "%$staff%")->_or_()
                    ->whereLike('CONCAT(b.lname," ",b.fname)', "%$staff%")->_or_()
                    ->whereLike('CONCAT(b.lname,", ",b.fname)', "%$staff%")->_or_()
                    ->whereLike('staff.staffID', "%$staff%")->_or_()
                    ->whereLike('b.altID', "%$staff%");

                $q = $hr->find(function($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('hr/index', [
            'title' => _t('Human Resources'),
            'search' => $q
            ]
        );
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $staff = $app->db->staff();
                $staff->set([
                        'schoolCode' => $app->req->post['schoolCode'],
                        'buildingCode' => $app->req->post['buildingCode'],
                        'officeCode' => $app->req->post['officeCode'],
                        'office_phone' => $app->req->post['office_phone'],
                        'deptCode' => $app->req->post['deptCode'],
                        'status' => $app->req->post['status']
                    ])
                    ->where('staffID = ?', $id)
                    ->update();

                $smeta = $app->db->staff_meta();
                $smeta->set([
                        'jobStatusCode' => $app->req->post['jobStatusCode'],
                        'jobID' => $app->req->post['jobID'],
                        'supervisorID' => $app->req->post['supervisorID'],
                        'staffType' => $app->req->post['staffType'],
                        'hireDate' => $app->req->post['hireDate'],
                        'startDate' => $app->req->post['startDate'],
                        'endDate' => ($app->req->post['endDate'] != '' ? $app->req->post['endDate'] : NULL)
                    ])
                    ->where('id = ?', $app->req->post['id'])->_and_()
                    ->where('staffID = ?', $id)
                    ->update();

                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'hr' . '/' . (int) $id . '/');
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

        try {
            $empl = $app->db->staff()
                ->select('staff.*,b.id AS sMetaID,b.jobStatusCode,b.jobID')
                ->select('b.supervisorID,b.staffType,b.hireDate')
                ->select('b.startDate,b.endDate,c.title')
                ->select('c.hourly_wage,c.weekly_hours')
                ->select('SUM(c.hourly_wage*c.weekly_hours*4) AS Monthly,d.prefix')
                ->_join('staff_meta', 'staff.staffID = b.staffID', 'b')
                ->_join('job', 'b.jobID = c.id', 'c')
                ->_join('person', 'staff.staffID = d.personID', 'd')
                ->where('staff.staffID = ?', $id)->_and_()
                ->where('b.hireDate = (SELECT MAX(hireDate) FROM staff_meta WHERE staffID = ?)', $id)
                ->findOne();
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
         */ elseif (count(_h($empl->staffID)) <= 0) {

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
                if (isset($app->req->post['addDate'])) {
                    $pg = $app->db->pay_grade();
                    foreach ($app->req->post as $k => $v) {
                        $pg->$k = $v;
                    }
                    $pg->save();

                    etsis_logger_activity_log_write('New Record', 'Pay Grade', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
                } else {
                    $pg = $app->db->pay_grade();
                    foreach ($app->req->post as $k => $v) {
                        $pg->$k = $v;
                    }
                    $pg->where('id = ?', _filter_input_int(INPUT_POST, 'id'));
                    $pg->update();

                    etsis_logger_activity_log_write('Update Record', 'Pay Grade', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
                }
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
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
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
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
                if (isset($app->req->post['addDate'])) {
                    $job = $app->db->job();
                    foreach ($app->req->post as $k => $v) {
                        $job->$k = $v;
                    }
                    $job->save();

                    etsis_logger_activity_log_write('New Record', 'Job', _filter_input_string(INPUT_POST, 'title'), get_persondata('uname'));
                } else {
                    $job = $app->db->job();
                    foreach ($app->req->post as $k => $v) {
                        $job->$k = $v;
                    }
                    $job->where('id = ?', _filter_input_int(INPUT_POST, 'id'));
                    $job->update();

                    etsis_logger_activity_log_write('Update Record', 'Job', _filter_input_string(INPUT_POST, 'title'), get_persondata('uname'));
                }
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
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

        try {
            $jobs = $app->db->job()->select('job.*,b.grade')
                ->_join('pay_grade', 'job.pay_grade = b.id', 'b');
            $q = $jobs->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
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
                foreach ($app->req->post as $k => $v) {
                    $position->$k = $v;
                }
                $position->save();

                etsis_logger_activity_log_write('New Record', 'Job Position', get_name($id), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
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
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
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
         */ elseif (count(_h($q[0]['staffID'])) <= 0) {

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
                foreach ($app->req->post as $k => $v) {
                    $position->$k = $v;
                }
                $position->where('id = ?', _filter_input_int(INPUT_POST, 'id'));
                $position->update();

                etsis_logger_activity_log_write('Update Record', 'Job Position', get_name($id), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
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

        try {
            $jobs = $app->db->staff_meta()
                ->select('staff_meta.*,b.title,b.hourly_wage')
                ->select('b.weekly_hours,c.grade')
                ->_join('job', 'staff_meta.jobID = b.id', 'b')
                ->_join('pay_grade', 'b.pay_grade = c.id ', 'c')
                ->where('staff_meta.staffID = ?', $id);
            $q = $jobs->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
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
         */ elseif (count(_h($q[0]['staffID'])) <= 0) {

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

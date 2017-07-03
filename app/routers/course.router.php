<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

/**
 * Course Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
/**
 * Before route checks to make sure the logged in user
 * us allowed to manage options/settings.
 */
$app->before('GET|POST', '/crse(.*)', function() {
    if (!is_user_logged_in()) {
        etsis_redirect(get_base_url() . 'login' . '/');
    }
});

$app->group('/crse', function() use ($app) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/', function() {
        if (!hasPermission('access_course_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $post = $app->req->post['crse'];
                $crse = $app->db->query("SELECT 
                    CASE currStatus 
                    WHEN 'A' THEN 'Active' 
                    WHEN 'I' THEN 'Inactive' 
                    WHEN 'P' THEN 'Pending' 
                    ELSE 'Obsolete' 
                    END AS 'Status',currStatus,courseID,courseCode,
                    courseShortTitle,startDate,endDate 
                    FROM course
                    WHERE courseCode LIKE ?
                    ORDER BY startDate DESC", [ "%$post%"]
                );

                $q = $crse->find(function($data) {
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
        $app->view->display('course/index', [
            'title' => 'Search Course',
            'crse' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_course_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app) {
        $course = get_course($id);

        if ($app->req->isPost()) {
            try {
                $crse = $app->db->course();
                $crse->courseNumber = $app->req->post['courseNumber'];
                $crse->courseCode = $app->req->post['subjectCode'] . '-' . $app->req->post['courseNumber'];
                $crse->subjectCode = $app->req->post['subjectCode'];
                $crse->deptCode = $app->req->post['deptCode'];
                $crse->courseDesc = $app->req->post['courseDesc'];
                $crse->creditType = $app->req->post['creditType'];
                $crse->minCredit = $app->req->post['minCredit'];
                $crse->maxCredit = $app->req->post['maxCredit'];
                $crse->increCredit = $app->req->post['increCredit'];
                $crse->courseLevelCode = $app->req->post['courseLevelCode'];
                $crse->acadLevelCode = $app->req->post['acadLevelCode'];
                $crse->courseShortTitle = $app->req->post['courseShortTitle'];
                $crse->courseLongTitle = $app->req->post['courseLongTitle'];
                $crse->startDate = $app->req->post['startDate'];
                $crse->endDate = ($app->req->post['endDate'] != '' ? $app->req->post['endDate'] : NULL);
                $crse->currStatus = $app->req->post['currStatus'];

                if ($course->currStatus !== $app->req->post['currStatus']) {
                    $crse->statusDate = \Jenssegers\Date\Date::now();
                }
                $crse->where('courseID = ?', (int) $id);

                /**
                 * Fires during the update of a course.
                 *
                 * @since 6.1.10
                 * @param object $crse Course object.
                 */
                $app->hook->do_action('update_course_db_table', $crse);
                $crse->update();

                etsis_cache_delete($id, 'crse');
                /**
                 * Is triggered after a course is updated.
                 * 
                 * @since 6.1.05
                 * @param object $crse Course object.
                 */
                $app->hook->do_action('post_update_crse', $crse);
                etsis_logger_activity_log_write('Update', 'Course', _h($course->courseCode), get_persondata('uname'));
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

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($course == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($course) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($course->courseID) <= 0) {

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
            etsis_register_script('maxlength');

            $app->view->display('course/view', [
                'title' => _h($course->courseShortTitle) . ' :: Course',
                'crse' => $course
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/prrl/', function() {
        if (!hasPermission('manage_business_rules')) {
            _etsis_flash()->error(_t('Permission denied to view the Prerequisite screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/(\d+)/prrl/', function ($id) use($app) {
        $course = get_course($id);

        if ($app->req->isPost()) {
            try {
                $crse = $app->db->course();
                $crse->set([
                        'preReq' => ($app->req->post['preReq'] != '' ? $app->req->post['preReq'] : NULL),
                        'rule' => ($app->req->post['rule'] != '' ? $app->req->post['rule'] : NULL),
                        'printText' => ($app->req->post['printText'] != '' ? $app->req->post['printText'] : NULL)
                    ])
                    ->where('courseID = ?', (int) $id)
                    ->update();

                etsis_cache_delete($id, 'crse');
                etsis_logger_activity_log_write('Update Record', 'Course Prerequisite', _h($course->courseCode), get_persondata('uname'));
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

        $q = $app->db->student()
            ->select('stuID')
            ->where('status = "A"');
        $stu = $q->find(function ($data) {
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
        if ($course == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($course) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($course->courseID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('querybuilder');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('course/prrl', [
                'title' => _h($course->courseShortTitle) . ' :: Course',
                'crse' => $course,
                'stu' => $stu
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('POST', '/(\d+)/prrl/test/', function() {
        if (!hasPermission('manage_business_rules')) {
            _etsis_flash()->error(_t('Permission denied to view the Prerequisite screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->post('/(\d+)/prrl/test/', function ($id) use($app) {
        try {
            $crse = get_course($id);
            $rule = _escape($crse->rule);
            $prrl = $app->db->query(
                    "SELECT v_sacp.stuID FROM v_sacp"
                    . " INNER JOIN stal ON v_sacp.stuID = stal.stuID AND v_sacp.prog = stal.acadProgCode"
                    . " INNER JOIN v_scrd ON v_sacp.stuID = v_scrd.stuID AND v_sacp.prog = v_scrd.prog"
                    . " WHERE v_sacp.stuID = ?"
                    . " AND $rule", [$app->req->post['stuID']]
                )
                ->findOne();

            if (_escape($prrl->stuID) <> $app->req->post['stuID']) {
                $dept = get_department(_escape($crse->deptCode));

                $message = _escape($crse->printText);
                $message = str_replace('{name}', get_name($app->req->post['stuID']), $message);
                $message = str_replace('{stuID}', get_alt_id($app->req->post['stuID']), $message);
                $message = str_replace('{course}', _h($crse->courseCode), $message);
                $message = str_replace('{deptName}', _h($dept->deptName), $message);
                $message = str_replace('{deptEmail}', _h($dept->deptEmail), $message);
                $message = str_replace('{deptPhone}', _h($dept->deptPhone), $message);
                _etsis_flash()->error($message, $app->req->server['HTTP_REFERER']);
            } else {
                _etsis_flash()->success(sprintf(_t('<strong>%s</strong> passed the prerequisite rule.'), get_name($app->req->post['stuID'])), $app->req->server['HTTP_REFERER']);
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'crse' . '/' . $id . '/prrl/');
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'crse' . '/' . $id . '/prrl/');
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'crse' . '/' . $id . '/prrl/');
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET', '/(\d+)/prrl/c/', function() {
        if (!hasPermission('manage_business_rules')) {
            _etsis_flash()->error(_t('Permission denied to view the Prerequisite screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->get('/(\d+)/prrl/c/', function ($id) use($app) {
        try {
            $clear = $app->db->course();
            $clear->set([
                    'rule' => NULL
                ])
                ->where('courseID = ?', $id)
                ->update();
            etsis_cache_delete($id, 'crse');
            _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'crse' . '/' . $id . '/prrl/');
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'crse' . '/' . $id . '/prrl/');
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'crse' . '/' . $id . '/prrl/');
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'crse' . '/' . $id . '/prrl/');
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/addnl/', function() {
        if (!hasPermission('access_course_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/(\d+)/addnl/', function ($id) use($app) {
        $course = get_course($id);

        if ($app->req->isPost()) {
            try {
                $crse = $app->db->course();
                foreach ($app->req->post as $k => $v) {
                    $crse->$k = $v;
                }
                $crse->where('courseID = ?', (int) $id);
                $crse->update();

                etsis_cache_delete($id, 'crse');
                /**
                 * Is triggered after course additional info is updated.
                 * 
                 * @since 6.1.05
                 * @param object $crse Course object.
                 */
                $app->hook->do_action('post_update_crse_addnl_info', $crse);
                etsis_logger_activity_log_write('Update Record', 'Course', _h($course->courseCode), get_persondata('uname'));
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

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($course == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($course) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($course->courseID) <= 0) {

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

            $app->view->display('course/addnl-info', [
                'title' => _h($course->courseShortTitle) . ' :: Course',
                'crse' => $course
                ]
            );
        }
    });

    /**
     * Before route checks to make sure the logged in user
     * us allowed to manage options/settings.
     */
    $app->before('GET|POST', '/add/', function() {
        if (!hasPermission('add_course')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/add/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $crse = $app->db->course();
                $crse->courseNumber = $app->req->post['courseNumber'];
                $crse->courseCode = $app->req->post['subjectCode'] . '-' . $app->req->post['courseNumber'];
                $crse->subjectCode = $app->req->post['subjectCode'];
                $crse->deptCode = $app->req->post['deptCode'];
                $crse->courseDesc = $app->req->post['courseDesc'];
                $crse->minCredit = $app->req->post['minCredit'];
                //$crse->maxCredit = $app->req->post['maxCredit'];
                //$crse->increCredit = $app->req->post['increCredit'];
                $crse->courseLevelCode = $app->req->post['courseLevelCode'];
                $crse->acadLevelCode = $app->req->post['acadLevelCode'];
                $crse->courseShortTitle = $app->req->post['courseShortTitle'];
                $crse->courseLongTitle = $app->req->post['courseLongTitle'];
                $crse->startDate = $app->req->post['startDate'];
                $crse->endDate = ($app->req->post['endDate'] != '' ? $app->req->post['endDate'] : NULL);
                $crse->currStatus = $app->req->post['currStatus'];
                $crse->statusDate = \Jenssegers\Date\Date::now();
                $crse->approvedDate = \Jenssegers\Date\Date::now();
                $crse->approvedBy = get_persondata('personID');

                /**
                 * Fires during the saving/creating of a course.
                 *
                 * @since 6.1.10
                 * @param array $crse Course object.
                 */
                $app->hook->do_action('save_course_db_table', $crse);
                $crse->save();

                $_id = $crse->lastInsertId();

                etsis_cache_flush_namespace('crse');

                $course = get_course($_id);
                /**
                 * Fires after a new course has been created.
                 * 
                 * @since 6.1.05
                 * @param object $course Course object.
                 */
                $app->hook->do_action('post_save_crse', $course);
                etsis_logger_activity_log_write('New Record', 'Course', $app->req->post['subjectCode'] . '-' . $app->req->post['courseNumber'], get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'crse' . '/' . (int) $_id . '/');
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
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datepicker');
        etsis_register_script('maxlength');

        $app->view->display('course/add', [
            'title' => 'Add Course'
            ]
        );
    });

    $app->post('/crseLookup/', function() use($app) {

        $crse = get_course($app->req->post['courseID']);

        $json = [
            'input#shortTitle' => _h($crse->courseShortTitle), 'input#minCredit' => _h($crse->minCredit),
            'input#courseLevel' => _h($crse->courseLevelCode)
        ];

        echo json_encode($json);
    });

    $app->post('/termLookup/', function() use($app) {
        try {
            $term = $app->db->term()->where('termCode = ?', $app->req->post['termCode']);
            $q = $term->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            foreach ($q as $v) {
                $json = [
                    'input#rTerm' => _h($v['reportingTerm'])
                ];
            }
            echo json_encode($json);
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
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/clone/', function() {
        if (!hasPermission('add_course')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->get('/(\d+)/clone/', function($id) use($app) {
        try {
            $crse = get_course($id);

            $clone = $app->db->course();
            $clone->insert([
                'courseNumber' => _h($crse->courseNumber),
                'courseCode' => _h($crse->courseCode),
                'subjectCode' => _h($crse->subjectCode),
                'deptCode' => _h($crse->deptCode),
                'courseDesc' => _h($crse->courseDesc),
                'creditType' => _h($crse->creditType),
                'minCredit' => _h($crse->minCredit),
                'maxCredit' => _h($crse->maxCredit),
                'increCredit' => _h($crse->increCredit),
                'acadLevelCode' => _h($crse->acadLevelCode),
                'courseLevelCode' => _h($crse->courseLevelCode),
                'courseLongTitle' => _h($crse->courseLongTitle) . ' (COPY)',
                'courseShortTitle' => _h($crse->courseShortTitle),
                'preReq' => _h($crse->preReq),
                'allowAudit' => _h($crse->allowAudit),
                'allowWaitlist' => _h($crse->allowWaitlist),
                'minEnroll' => _h($crse->minEnroll),
                'seatCap' => _h($crse->seatCap),
                'startDate' => _h($crse->startDate),
                'currStatus' => _h($crse->currStatus),
                'statusDate' => \Jenssegers\Date\Date::now(),
                'approvedDate' => \Jenssegers\Date\Date::now(),
                'approvedBy' => get_persondata('personID')
            ]);

            $_id = $clone->lastInsertId();
            etsis_cache_flush_namespace('crse');
            etsis_logger_activity_log_write('New Record', 'Cloned Course', _h($crse->courseCode), get_persondata('uname'));
            _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'crse' . '/' . (int) $_id . '/');
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        }
    });

    $app->post('/dept/', function() use($app) {
        try {
            etsis_cache_flush_namespace('dept');
            $dept = $app->db->department();
            foreach ($app->req->post as $k => $v) {
                $dept->$k = $v;
            }
            $dept->save();
            $_id = $dept->lastInsertId();

            $department = $app->db->department()
                ->where('id = ?', $_id);
            $q = $department->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            echo json_encode($q);
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
    });

    $app->post('/subj/', function() use($app) {
        try {
            etsis_cache_flush_namespace('subj');
            $subj = $app->db->subject();
            foreach ($app->req->post as $k => $v) {
                $subj->$k = $v;
            }
            $subj->save();
            $_id = $subj->lastInsertId();

            $subject = $app->db->subject()
                ->where('id = ?', $_id);
            $q = $subject->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            echo json_encode($q);
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
    });

    $app->match('GET|POST', '/stuLookup/', function() use($app) {
        try {
            $term = $app->req->get['term'];
            $stu = $app->db->student()
                ->select('student.stuID,person.altID,person.fname,person.lname')
                ->_join('person', 'student.stuID = person.personID')
                ->whereLike('student.stuID', "%" . $term . "%")->_or_()
                ->whereLike('person.altID', "%" . $term . "%")->_or_()
                ->whereLike('person.fname', "%" . $term . "%")->_or_()
                ->whereLike('person.lname', "%" . $term . "%")->_or_()
                ->whereLike('CONCAT(person.lname,", ",person.fname)', "%" . $term . "%")->_or_()
                ->whereLike('CONCAT(person.fname," ",person.lname)', "%" . $term . "%")
                ->find();
            $items = [];
            foreach ($stu as $x) {
                $option = array(
                    'id' => _h($x->stuID),
                    'label' => get_name(_h($x->stuID)),
                    'value' => get_name(_h($x->stuID))
                );
                $items[] = $option;
            }
            echo json_encode($items);
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
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
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
    if (!isUserLoggedIn()) {
        redirect(get_base_url() . 'login' . '/');
    }

    /**
     * If user is logged in and the lockscreen cookie is set, 
     * redirect user to the lock screen until he/she enters 
     * his/her password to gain access.
     */
    if (isset($_COOKIE['SCREENLOCK'])) {
        redirect(get_base_url() . 'lock' . '/');
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
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-maxlength/bootstrap-maxlength.min.js',
    'components/modules/admin/forms/elements/bootstrap-maxlength/custom/js/custom.js'
];

$json_url = get_base_url() . 'api' . '/';

$logger = new \app\src\Log();
$flashNow = new \app\src\Core\etsis_Messages();

$app->group('/crse', function() use ($app, $css, $js, $json_url, $logger, $flashNow) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/', function() {
        if (!hasPermission('access_course_screen')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/', function () use($app, $css, $js) {
        if ($app->req->isPost()) {
            $post = $_POST['crse'];
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
        }
        $app->view->display('course/index', [
            'title' => 'Search Course',
            'cssArray' => $css,
            'jsArray' => $js,
            'crse' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_course_screen')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        $course = get_course($id);

        if ($app->req->isPost()) {
            $crse = $app->db->course();
            $crse->courseNumber = $_POST['courseNumber'];
            $crse->courseCode = $_POST['subjectCode'] . '-' . $_POST['courseNumber'];
            $crse->subjectCode = $_POST['subjectCode'];
            $crse->deptCode = $_POST['deptCode'];
            $crse->courseDesc = $_POST['courseDesc'];
            $crse->creditType = $_POST['creditType'];
            $crse->minCredit = $_POST['minCredit'];
            $crse->maxCredit = $_POST['maxCredit'];
            $crse->increCredit = $_POST['increCredit'];
            $crse->courseLevelCode = $_POST['courseLevelCode'];
            $crse->acadLevelCode = $_POST['acadLevelCode'];
            $crse->courseShortTitle = $_POST['courseShortTitle'];
            $crse->courseLongTitle = $_POST['courseLongTitle'];
            $crse->startDate = $_POST['startDate'];
            $crse->endDate = $_POST['endDate'];
            $crse->currStatus = $_POST['currStatus'];

            if ($course->currStatus !== $_POST['currStatus']) {
                $crse->statusDate = $app->db->NOW();
            }
            $crse->where('courseID = ?', (int) $id);

            /**
             * Fires during the update of a course.
             *
             * @since 6.1.10
             * @param object $crse Course object.
             */
            $app->hook->do_action('update_course_db_table', $crse);

            if ($crse->update()) {
                etsis_cache_delete($id, 'crse');
                /**
                 * Is triggered after a course is updated.
                 * 
                 * @since 6.1.05
                 * @param object $crse Course object.
                 */
                $app->hook->do_action('post_update_crse', $crse);
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update', 'Course', $course->courseCode, get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                $logger->setLog('Update Error', 'Course', $course->courseCode, get_persondata('uname'));
            }
            redirect($app->req->server['HTTP_REFERER']);
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
         */ elseif (count($course->courseID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('course/view', [
                'title' => $course->courseShortTitle . ' :: Course',
                'cssArray' => $css,
                'jsArray' => $js,
                'crse' => $course
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addnl/(\d+)/', function() {
        if (!hasPermission('access_course_screen')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/addnl/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        $course = get_course($id);

        if ($app->req->isPost()) {
            $crse = $app->db->course();
            foreach ($_POST as $k => $v) {
                $crse->$k = $v;
            }
            $crse->where('courseID = ?', (int) $id);
            if ($crse->update()) {
                etsis_cache_delete($id, 'crse');
                /**
                 * Is triggered after course additional info is updated.
                 * 
                 * @since 6.1.05
                 * @param object $crse Course object.
                 */
                $app->hook->do_action('post_update_crse_addnl_info', $crse);
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Course', $course->courseCode, get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect($app->req->server['HTTP_REFERER']);
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
         */ elseif (count($course->courseID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('course/addnl-info', [
                'title' => $course->courseShortTitle . ' :: Course',
                'cssArray' => $css,
                'jsArray' => $js,
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
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/add/', function () use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            $crse = $app->db->course();
            $crse->courseNumber = $_POST['courseNumber'];
            $crse->courseCode = $_POST['subjectCode'] . '-' . $_POST['courseNumber'];
            $crse->subjectCode = $_POST['subjectCode'];
            $crse->deptCode = $_POST['deptCode'];
            $crse->courseDesc = $_POST['courseDesc'];
            $crse->minCredit = $_POST['minCredit'];
            //$crse->maxCredit = $_POST['maxCredit'];
            //$crse->increCredit = $_POST['increCredit'];
            $crse->courseLevelCode = $_POST['courseLevelCode'];
            $crse->acadLevelCode = $_POST['acadLevelCode'];
            $crse->courseShortTitle = $_POST['courseShortTitle'];
            $crse->courseLongTitle = $_POST['courseLongTitle'];
            $crse->startDate = $_POST['startDate'];
            $crse->endDate = $_POST['endDate'];
            $crse->currStatus = $_POST['currStatus'];
            $crse->statusDate = $app->db->NOW();
            $crse->approvedDate = $app->db->NOW();
            $crse->approvedBy = get_persondata('personID');

            /**
             * Fires during the saving/creating of a course.
             *
             * @since 6.1.10
             * @param array $crse Course object.
             */
            $app->hook->do_action('save_course_db_table', $crse);

            if ($crse->save()) {
                $ID = $crse->lastInsertId();

                etsis_cache_flush_namespace('crse');

                $course = get_course($ID);
                /**
                 * Fires after a new course has been created.
                 * 
                 * @since 6.1.05
                 * @param object $course Course object.
                 */
                $app->hook->do_action('post_save_crse', $course);

                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Course', $_POST['subjectCode'] . '-' . $_POST['courseNumber'], get_persondata('uname'));
                redirect(get_base_url() . 'crse' . '/' . (int) $ID . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        $app->view->display('course/add', [
            'title' => 'Add Course',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    $app->post('/crseLookup/', function() use($app) {

        $crse = get_course($_POST['courseID']);

        $json = [
            'input#shortTitle' => $crse->courseShortTitle, 'input#minCredit' => $crse->minCredit,
            'input#courseLevel' => $crse->courseLevelCode
        ];

        echo json_encode($json);
    });

    $app->post('/termLookup/', function() use($app) {

        $term = $app->db->term()->where('termCode = ?', $_POST['termCode']);
        $q = $term->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        foreach ($q as $v) {
            $json = [
                'input#rTerm' => $v['reportingTerm']
            ];
        }
        echo json_encode($json);
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/clone/(\d+)/', function() {
        if (!hasPermission('add_course')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->get('/clone/(\d+)/', function($id) use($app, $flashNow, $logger) {
        $crse = get_course($id);
        $clone = $app->db->course();
        $clone->courseNumber = $crse->courseNumber;
        $clone->courseCode = $crse->courseCode;
        $clone->subjectCode = $crse->subjectCode;
        $clone->deptCode = $crse->deptCode;
        $clone->courseDesc = $crse->courseDesc;
        $clone->creditType = $crse->creditType;
        $clone->minCredit = $crse->minCredit;
        $clone->maxCredit = $crse->maxCredit;
        $clone->increCredit = $crse->increCredit;
        $clone->acadLevelCode = $crse->acadLevelCode;
        $clone->courseLevelCode = $crse->courseLevelCode;
        $clone->courseLongTitle = $crse->courseLongTitle . ' (COPY)';
        $clone->courseShortTitle = $crse->courseShortTitle;
        $clone->preReq = $crse->preReq;
        $clone->allowAudit = $crse->allowAudit;
        $clone->allowWaitlist = $crse->allowWaitlist;
        $clone->minEnroll = $crse->minEnroll;
        $clone->seatCap = $crse->seatCap;
        $clone->startDate = $crse->startDate;
        $clone->currStatus = $crse->currStatus;
        $clone->statusDate = $app->db->NOW();
        $clone->approvedDate = $app->db->NOW();
        $clone->approvedBy = get_persondata('personID');

        if ($clone->save()) {
            $ID = $clone->lastInsertId();
            etsis_cache_flush_namespace('crse');
            $app->flash('success_message', $flashNow->notice(200));
            $logger->setLog('New Record', 'Cloned Course', $crse->courseCode, get_persondata('uname'));
            redirect(get_base_url() . 'crse' . '/' . (int) $ID . '/');
        } else {
            $app->flash('error_message', $flashNow->notice(409));
            redirect($app->req->server['HTTP_REFERER']);
        }
    });

    $app->post('/dept/', function() use($app) {
        etsis_cache_flush_namespace('dept');
        $dept = $app->db->department();
        foreach ($_POST as $k => $v) {
            $dept->$k = $v;
        }
        $dept->save();
        $ID = $dept->lastInsertId();

        $department = $app->db->department()
            ->where('deptID = ?', $ID);
        $q = $department->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        echo json_encode($q);
    });

    $app->post('/subj/', function() use($app) {
        etsis_cache_flush_namespace('subj');
        $subj = $app->db->subject();
        foreach ($_POST as $k => $v) {
            $subj->$k = $v;
        }
        $subj->save();
        $ID = $subj->lastInsertId();

        $subject = $app->db->subject()
            ->where('subjectID = ?', $ID);
        $q = $subject->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        echo json_encode($q);
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

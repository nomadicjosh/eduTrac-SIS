<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Course Section Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
/**
 * Before route check.
 */
$app->before('GET|POST', '/sect(.*)', function() {
    /**
     * If user is logged in and the lockscreen cookie is set, 
     * redirect user to the lock screen until he/she enters 
     * his/her password to gain access.
     */
    if (isset($_COOKIE['SCREENLOCK'])) {
        redirect(get_base_url() . 'lock' . DS);
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
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/jCombo/jquery.jCombo.min.js',
    'components/modules/admin/forms/elements/bootstrap-maxlength/bootstrap-maxlength.min.js',
    'components/modules/admin/forms/elements/bootstrap-maxlength/custom/js/custom.js'
];

$json_url = get_base_url() . 'api' . DS;

$logger = new \app\src\Log();
$cache = new \app\src\Cache();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();

$app->group('/sect', function() use ($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/', function() {
        if (!hasPermission('access_course_sec_screen')) {
            redirect(get_base_url() . 'dashboard' . DS);
        }
    });

    $app->match('GET|POST', '/', function () use($app, $css, $js) {
        if ($app->req->isPost()) {
            $post = $_POST['sect'];
            $sect = $app->db->query("SELECT 
                    CASE a.currStatus 
                    WHEN 'A' THEN 'Active' 
                    WHEN 'I' THEN 'Inactive' 
                    WHEN 'P' THEN 'Pending' 
                    WHEN 'C' THEN 'Cancelled' 
                    ELSE 'Obsolete'
                    END AS 'Status', 
                        a.courseSecCode,a.secShortTitle,a.courseSecID,a.termCode 
                    FROM course_sec a 
                    WHERE courseSection LIKE ? 
                    ORDER BY a.termCode DESC", [ "%$post%"]
            );

            $q = $sect->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        }
        $app->view->display('section/index', [
            'title' => 'Search Course Section',
            'cssArray' => $css,
            'jsArray' => $js,
            'sect' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_course_sec_screen')) {
            redirect(get_base_url() . 'dashboard' . DS);
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {
        /**
         * Fires before the course section has been updated.
         * 
         * @since 6.1.07
         * @param int $id Primary key of the course section.
         */
        do_action('pre_update_course_sec', $id);

        $section = $app->db->course_sec()->where('courseSecID = ?', (int) $id)->findOne();

        $date = date("Y-m-d");
        $time = date("h:i A");

        if ($app->req->isPost()) {
            $term = str_replace("/", "", $_POST['termCode']);

            $sect = $app->db->course_sec();
            /**
             * Fires during the update of a course section.
             * 
             * @since 6.1.10
             * @param array $sect Course section data object.
             */
            do_action('update_course_sec_db_table', $sect);
            $sect->locationCode = $_POST['locationCode'];
            $sect->termCode = $_POST['termCode'];
            $sect->secShortTitle = $_POST['secShortTitle'];
            $sect->startDate = $_POST['startDate'];
            $sect->endDate = $_POST['endDate'];
            $sect->deptCode = $_POST['deptCode'];
            $sect->minCredit = $_POST['minCredit'];
            $sect->comment = $_POST['comment'];
            $sect->courseSection = $_POST['termCode'] . '-' . $_POST['courseSecCode'];
            $sect->ceu = $_POST['ceu'];
            $sect->courseLevelCode = $_POST['courseLevelCode'];
            $sect->acadLevelCode = $_POST['acadLevelCode'];
            $sect->where('courseSecID = ?', (int) $id);

            $da = $app->db->term()->where('termCode = ?', $section->termCode)->findOne();

            if ($section->currStatus != $_POST['currStatus']) {
                /**
                 * If the posted status is 'C' and today's date is less than the 
                 * primary term start date, then delete all student course sec as well as 
                 * student acad cred records.
                 */
                if ($_POST['currStatus'] == 'C' && $date < $da->termStartDate) {
                    $q = $app->db->course_sec();
                    $q->currStatus = $_POST['currStatus'];
                    $q->statusDate = $date;
                    $q->where('courseSecID = ?', $id);

                    $app->db->stu_course_sec()->where('courseSecID = ?', $id)->delete();
                    $app->db->stu_acad_cred()->where('courseSecID = ?', $id)->delete();
                }
                /**
                 * If posted status is 'C' and today's date is greater than equal to the 
                 * primary term start date, then update student course sec records as 
                 * well as the student academic credit records with a 'C' status and 
                 * update the status date and time.
                 */ elseif ($_POST['currStatus'] == 'C' && $date >= $da->termStartDate) {
                    $q = $app->db->course_sec();
                    $q->currStatus = $_POST['currStatus'];
                    $q->statusDate = $date;
                    $q->where('courseSecID = ?', $id);

                    $sql1 = $app->db->stu_course_sec();
                    $sql1->status = $_POST['currStatus'];
                    $sql1->statusDate = $date;
                    $sql1->statusTime = $time;
                    $sql1->where('courseSecID = ?', $id)->update();

                    $sql2 = $app->db->stu_acad_cred();
                    $sql2->status = $_POST['currStatus'];
                    $sql2->statusDate = $date;
                    $sql2->statusTime = $time;
                    $sql2->where('courseSecID = ?', $id)->update();
                }
                /**
                 * If the status is different from 'C', update the status and status date
                 * as long as there are
                 */ else {
                    $q = $app->db->course_sec();
                    $q->currStatus = $_POST['currStatus'];
                    $q->statusDate = $date;
                    $q->where('courseSecID = ?', $id);
                }
            }

            if ($sect->update() || $q->update()) {
                /** Delete db cache if the data was updated successfully */
                $dbcache->clearCache("course_sec-$id");
                $dbcache->clearCache("crseCatalog");
                $dbcache->clearCache("$term-catalog");
                $logger->setLog('Update Record', 'Course Section', $_POST['secShortTitle'] . ' (' . $_POST['termCode'] . '-' . $section->courseSecCode . ')', get_persondata('uname'));
                $app->flash('success_message', $flashNow->notice(200));
            } else {
                $logger->setLog('Update Error', 'Course Section', $_POST['secShortTitle'] . ' (' . $_POST['termCode'] . '-' . $section->courseSecCode . ')', get_persondata('uname'));
                $app->flash('error_message', $flashNow->notice(409));
            }
            /**
             * Query course section after it has been updated.
             * 
             * @since 6.1.07
             */
            $section = $app->db->course_sec()
                ->setTableAlias('sect')
                ->select('sect.*,crse.subjectCode,crse.deptCode,crse.creditType')
                ->select('crse.courseShortTitle,crse.courseLongTitle')
                ->_join('course', 'sect.courseID = crse.courseID', 'crse')
                ->where('courseSecID = ?', $id)
                ->findOne();
            /**
             * Fires after the course section has been updated.
             * 
             * @since 6.1.07
             * @param array $sect Course section data object.
             */
            do_action('post_update_course_sec', $section);
            redirect(get_base_url() . 'sect' . DS . $id . '/');
        }

        $preReq = $app->db->course()->select('preReq')->where('courseID = ?', $section->courseID);
        $req = $preReq->find(function($data) {
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
        if ($section == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($section) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($section->courseSecID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/view', [
                'title' => $section->secShortTitle . ' :: Course Section',
                'cssArray' => $css,
                'jsArray' => $js,
                'sect' => $section,
                'req' => $req
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/add/(\d+)/', function() {
        if (!hasPermission('add_course_sec')) {
            redirect(get_base_url() . 'dashboard' . DS);
        }
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        /**
         * Fires before a course section has been created.
         * 
         * @since 6.1.07
         * @param int $id Primary key of the course from which the course section is created.
         */
        do_action('pre_save_course_sec', $id);
        
        $crse = $app->db->course()->where('courseID = ?', $id)->findOne();

        if ($app->req->isPost()) {
            $sc = $crse->courseCode . '-' . $_POST['sectionNumber'];
            $courseSection = $_POST['termCode'] . '-' . $crse->courseCode . '-' . $_POST['sectionNumber'];

            $dotw = '';
            /** Combine the days of the week to be entered into the database */
            $days = $_POST['dotw'];
            for ($i = 0; $i < sizeof($days); $i++) {
                $dotw .= $days[$i];
            }

            $sect = $app->db->course_sec();
            /**
             * Fires during the saving/creating of a course section.
             * 
             * @since 6.1.10
             * @param array $sect Course section data object.
             */
            do_action('save_course_sec_db_table', $sect);
            $sect->sectionNumber = $_POST['sectionNumber'];
            $sect->courseSecCode = _trim($sc);
            $sect->courseSection = _trim($courseSection);
            $sect->buildingCode = 'NULL';
            $sect->roomCode = 'NULL';
            $sect->locationCode = $_POST['locationCode'];
            $sect->courseLevelCode = $_POST['courseLevelCode'];
            $sect->acadLevelCode = $_POST['acadLevelCode'];
            $sect->deptCode = $_POST['deptCode'];
            $sect->termCode = $_POST['termCode'];
            $sect->courseID = $id;
            $sect->courseCode = $crse->courseCode;
            $sect->secShortTitle = $_POST['secShortTitle'];
            $sect->startDate = $_POST['startDate'];
            $sect->endDate = $_POST['endDate'];
            $sect->minCredit = $_POST['minCredit'];
            $sect->ceu = $_POST['ceu'];
            $sect->secType = $_POST['secType'];
            $sect->instructorMethod = $_POST['instructorMethod'];
            $sect->dotw = $dotw;
            $sect->startTime = $_POST['startTime'];
            $sect->endTime = $_POST['endTime'];
            $sect->webReg = $_POST['webReg'];
            $sect->currStatus = $_POST['currStatus'];
            $sect->statusDate = $app->db->NOW();
            $sect->comment = $_POST['comment'];
            $sect->approvedDate = $app->db->NOW();
            $sect->approvedBy = get_persondata('personID');
            if ($sect->save()) {
                $ID = $sect->lastInsertId();
                $section = [
                    "sectionNumber" => _trim($_POST['sectionNumber']), "courseSecCode" => _trim($sc),
                    "courseID" => $_POST['courseID'], "locationCode" => _trim($_POST['locationCode']),
                    "termCode" => _trim($_POST['termCode']), "courseCode" => _trim($_POST['courseCode']), "secShortTitle" => $_POST['secShortTitle'],
                    "startDate" => $_POST['startDate'], "endDate" => $_POST['endDate'], "deptCode" => _trim($_POST['deptCode']),
                    "minCredit" => $_POST['minCredit'], "ceu" => $_POST['ceu'], "courseSection" => _trim($courseSection),
                    "courseLevelCode" => _trim($_POST['courseLevelCode']), "acadLevelCode" => _trim($_POST['acadLevelCode']),
                    "currStatus" => $_POST['currStatus'], "statusDate" => $_POST['statusDate'], "comment" => $_POST['comment'],
                    "approvedDate" => $_POST['approvedDate'], "approvedBy" => $_POST['approvedBy'], "secLongTitle" => $crse->courseLongTitle,
                    "section" => _trim($courseSection), "description" => $crse->courseDesc
                ];
                /**
                 * Fires after a course section has been created.
                 * 
                 * @since 6.1.07
                 * @param array $section Course section data object.
                 */
                do_action('post_save_course_sec', $section);

                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Course Section', _trim($courseSection), get_persondata('uname'));
                redirect(get_base_url() . 'sect' . DS . $ID . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($crse == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($crse) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($crse->courseID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {
            $app->view->display('section/add', [
                'title' => 'Create Section',
                'cssArray' => $css,
                'jsArray' => $js,
                'sect' => $crse
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addnl/(\d+)/', function() {
        if (!hasPermission('access_course_sec_screen')) {
            redirect(get_base_url() . 'dashboard' . DS);
        }
    });

    $app->match('GET|POST', '/addnl/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        /**
         * Fires before course section additional
         * information has been updated.
         * 
         * @since 6.1.07
         * @param int $id Primary key of the course section.
         */
        do_action('pre_course_sec_addnl', $id);

        $section = $app->db->course_sec()->where('courseSecID = ?', (int) $id)->findOne();

        if ($app->req->isPost()) {
            $sect = $app->db->course_sec();
            foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                $sect->$k = $v;
            }
            $sect->where('courseSecID = ?', $id);
            if ($sect->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Course Section', $section->courseSection, get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            /**
             * Query course section after it has been updated.
             * 
             * @since 6.1.07
             */
            $section = $app->db->course_sec()
                ->setTableAlias('sect')
                ->select('sect.*,crse.subjectCode,crse.deptCode,crse.creditType')
                ->select('crse.courseShortTitle,crse.courseLongTitle')
                ->_join('course', 'sect.courseID = crse.courseID', 'crse')
                ->where('courseSecID = ?', $id)
                ->findOne();
            /**
             * Fires after course section additional
             * information has been updated.
             * 
             * @since 6.1.07
             * @param array $section Course section data object.
             */
            do_action('post_course_sec_addnl', $section);
            redirect($app->req->server['HTTP_REFERER']);
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($section == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($section) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($section->courseSecID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/addnl-info', [
                'title' => $section->secShortTitle . ' :: Course Section',
                'cssArray' => $css,
                'jsArray' => $js,
                'sect' => $section
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/soff/(\d+)/', function() {
        if (!hasPermission('access_course_sec_screen')) {
            redirect(get_base_url() . 'dashboard' . DS);
        }
    });

    $app->match('GET|POST', '/soff/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {
        $sect = $app->db->course_sec()->where('courseSecID = ?', (int) $id)->findOne();

        if ($app->req->isPost()) {
            $dotw = '';
            /** Combine the days of the week to be entered into the database */
            $days = $_POST['dotw'];
            for ($i = 0; $i < sizeof($days); $i++) {
                $dotw .= $days[$i];
            }

            $soff = $app->db->course_sec();
            $soff->buildingCode = $_POST['buildingCode'];
            $soff->roomCode = $_POST['roomCode'];
            $soff->dotw = $dotw;
            $soff->startTime = $_POST['startTime'];
            $soff->endTime = $_POST['endTime'];
            $soff->webReg = $_POST['webReg'];
            $soff->where('courseSecID = ?', $id);

            if ($soff->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $dbcache->clearCache("course_sec-$id");
                $logger->setLog('Update Record', 'Course Section Offering', $sect->courseSection, get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect(get_base_url() . 'sect/soff' . DS . (int) $id . '/');
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($sect == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($sect) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($sect->courseSecID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/offering-info', [
                'title' => $sect->secShortTitle . ' :: Course Section',
                'cssArray' => $css,
                'jsArray' => $js,
                'sect' => $sect
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/fgrade/(\d+)/', function() {
        if (!hasPermission('submit_final_grades')) {
            redirect(get_base_url() . 'dashboard' . DS);
        }
    });

    $app->match('GET|POST', '/fgrade/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            $size = count($_POST['stuID']);
            $i = 0;
            while ($i < $size) {
                $grade = $app->db->stu_acad_cred();
                $grade->grade = $_POST['grade'][$i];
                $grade->gradePoints = acadCredGradePoints($_POST['grade'][$i], $_POST['attCredit']);
                $grade->where('stuID = ?', $_POST['stuID'][$i])->_and_()->where('courseSecID = ?', $id)->update();

                if (acadCredGradePoints($_POST['grade'][$i], $_POST['attCredit']) > 0) {
                    $compCred = $_POST['attCredit'];
                } else {
                    $compCred = '0';
                }

                $grade = $app->db->stu_acad_cred();
                $grade->compCred = $compCred;
                $grade->where('stuID = ?', $_POST['stuID'][$i])->_and_()->where('courseSecID = ?', $id)->update();

                $logger->setLog('Update Record', 'Final Grade', get_name($_POST['stuID'][$i]) . ' (' . $_POST['termCode'] . '-' . $_POST['courseSecCode'] . ')', get_persondata('uname'));
                ++$i;
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $sect = $app->db->course_sec()->where('courseSecID = ?', (int) $id)->findOne();

        $fgrade = $app->db->course_sec()
            ->select('course_sec.courseSecID,course_sec.secShortTitle,course_sec.minCredit,course_sec.courseSection,course_sec.facID')
            ->select('b.stuID,b.courseSecCode,b.courseSection,b.termCode,b.grade')
            ->_join('stu_acad_cred', 'course_sec.courseSecID = b.courseSecID', 'b')
            ->where('course_sec.courseSecID = ?', $id);
        $q = $fgrade->find(function($data) {
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
         */ elseif (count($q[0]['courseSecID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/section-fgrade', [
                'title' => $q[0]['courseSection'] . ' :: Section Final Grades',
                'cssArray' => $css,
                'jsArray' => $js,
                'grade' => $q,
                'sect' => $sect
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/rgn/', function() {
        if (!hasPermission('register_students')) {
            redirect(get_base_url() . 'dashboard' . DS);
        }
    });

    $app->match('GET|POST', '/rgn/', function () use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {

        /**
         * Fires before a student is registered into
         * a course by a staff member.
         * 
         * @since 6.1.07
         */
        do_action('pre_rgn_stu_crse_reg');

        $time = date("h:i A");

        if ($app->req->isPost()) {
            $sect = $app->db->course_sec()->where('courseSecID = ?', (int) $_POST['courseSecID'])->findOne();
            $crse = $app->db->course()->where('courseID = ?', (int) $sect->courseID)->findOne();
            $term = $app->db->term()->where('termCode = ?', $sect->termCode)->findOne();

            $stcs = $app->db->stu_course_sec();
            $stcs->stuID = $_POST['stuID'];
            $stcs->courseSecID = $sect->courseSecID;
            $stcs->courseSecCode = $sect->courseSecCode;
            $stcs->courseSection = $sect->courseSection;
            $stcs->termCode = $sect->termCode;
            $stcs->courseCredits = $sect->minCredit;
            $stcs->ceu = $sect->ceu;
            $stcs->status = 'A';
            $stcs->regDate = $app->db->NOW();
            $stcs->regTime = date("h:i A");
            $stcs->statusDate = $app->db->NOW();
            $stcs->statusTime = $time;
            $stcs->addedBy = get_persondata('personID');

            $stac = $app->db->stu_acad_cred();
            $stac->stuID = $_POST['stuID'];
            $stac->courseID = $sect->courseID;
            $stac->courseSecID = $sect->courseSecID;
            $stac->courseCode = $sect->courseCode;
            $stac->courseSecCode = $sect->courseSecCode;
            $stac->sectionNumber = $sect->sectionNumber;
            $stac->courseSection = $sect->courseSection;
            $stac->termCode = $sect->termCode;
            $stac->reportingTerm = $term->reportingTerm;
            $stac->subjectCode = $crse->subjectCode;
            $stac->deptCode = $sect->deptCode;
            $stac->shortTitle = $crse->courseShortTitle;
            $stac->longTitle = $crse->courseLongTitle;
            $stac->attCred = $sect->minCredit;
            $stac->status = 'A';
            $stac->statusDate = $app->db->NOW();
            $stac->statusTime = $time;
            $stac->acadLevelCode = $sect->acadLevelCode;
            $stac->courseLevelCode = $sect->courseLevelCode;
            $stac->startDate = $sect->startDate;
            $stac->endDate = $sect->endDate;
            $stac->addedBy = get_persondata('personID');
            $stac->addDate = $app->db->NOW();

            if ($stcs->save() && $stac->save()) {
                /**
                 * @since 6.1.07
                 */
                $ID = $stac->lastInsertId();
                $sacd = $app->db->stu_acad_cred()
                    ->setTableAlias('stac')
                    ->select('stac.*,nae.uname,nae.fname,nae.lname,nae.email')
                    ->_join('person', 'stac.stuID = nae.personID', 'nae')
                    ->where('stac.stuAcadCredID = ?', $ID)
                    ->findOne();
                /**
                 * Fires after a student has been registered into
                 * a course by a staff member.
                 * 
                 * @since 6.1.07
                 * @param array $sacd Student Academic Credit detail data object.
                 */
                do_action('post_rgn_stu_crse_reg', $sacd);

                if (function_exists('financial_module')) {
                    /**
                     * Generate bill and/or add fees.
                     */
                    generate_stu_bill($sect->termCode, $_POST['stuID'], $sect->courseSecID);
                }
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Course Registration Via Staff', get_name($_POST['stuID']) . ' - ' . $sect->secShortTitle, get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect(get_base_url() . 'sect/rgn' . DS);
        }

        $app->view->display('section/register', [
            'title' => 'Course Registration',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/sros.*', function() {
        if (!hasPermission('access_stu_roster_screen')) {
            redirect(get_base_url() . 'dashboard' . DS);
        }
    });

    $app->match('GET|POST', '/sros/', function () use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            redirect(get_base_url() . 'sect/sros' . DS . $_POST['sectionID'] . '/' . $_POST['template'] . '/');
        }

        $app->view->display('section/sros', [
            'title' => 'Course Section Roster',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    $app->get('/sros/(\d+)/(\w+)/', function ($id, $template) use($app, $css, $js, $logger, $flashNow) {

        $sros = $app->db->query("SELECT 
						a.stuID,a.courseSecCode,a.termCode,a.courseCredits,
					CASE a.status 
					WHEN 'A' THEN 'Add' 
					WHEN 'N' THEN 'New'
					ELSE 'Drop' 
					END AS 'Status',
						b.acadProgCode,b.acadLevelCode,c.courseSection,
						c.facID,c.roomCode,c.secShortTitle,c.startDate,
						c.endDate,c.startTime,c.endTime,c.dotw,
						c.instructorMethod 
					FROM stu_course_sec a 
					LEFT JOIN stu_acad_level b ON a.stuID = b.stuID 
					LEFT JOIN course_sec c ON a.courseSecID = c.courseSecID 
					WHERE c.courseSecID = ? 
					AND c.termCode = a.termCode 
					AND a.status IN('A','N','D') 
					AND b.addDate = (SELECT MAX(addDate) FROM stu_program WHERE stuID = a.stuID) 
					GROUP BY a.stuID,a.courseSecCode,a.termCode", [ $id]);
        $q = $sros->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $stu = $app->db->stu_course_sec()->select('COUNT(stu_course_sec.stuID) AS count')
            ->where('courseSecID = ?', $id)->_and_()
            ->whereIn('status', ['A', 'N', 'D']);

        $count = $stu->find(function($data) {
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
         */ elseif (count($q[0]['stuID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/templates/roster/' . $template . '.template', [
                'cssArray' => $css,
                'jsArray' => $js,
                'sros' => $q,
                'count' => $count
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/catalog.*', function() {
        if (!hasPermission('access_course_sec_screen')) {
            redirect(get_base_url() . 'dashboard' . DS);
        }
    });

    $app->get('/catalog/', function () use($app, $css, $js) {

        $cat = $app->db->course_sec()
            ->select('course_sec.termCode,COUNT(course_sec.courseSecCode) as Courses,b.termName')
            ->_join('term', 'course_sec.termCode = b.termCode', 'b')
            ->where('course_sec.currStatus = "A"')
            ->groupBy('course_sec.termCode')
            ->orderBy('course_sec.termCode', 'DESC');

        $q = $cat->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('section/catalog', [
            'title' => 'Course Catalogs',
            'cssArray' => $css,
            'jsArray' => $js,
            'catalog' => $q
            ]
        );
    });

    $app->get('/catalog/(.*)/', function ($term) use($app, $css, $js) {

        $cat = $app->db->course_sec()
            ->select('courseSecCode,termCode,secShortTitle,facID')
            ->select('dotw,startTime,endTime,buildingCode,roomCode')
            ->select('locationCode,minCredit')
            ->where('termCode = ?', $term)
            ->orderBy('courseSecCode');

        $q = $cat->find(function($data) {
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
         */ elseif (count($q[0]['courseSecCode']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/catalog-pdf', [
                'cssArray' => $css,
                'jsArray' => $js,
                'catalog' => $q
                ]
            );
        }
    });

    $app->post('/secTermLookup/', function() use($app) {
        $term = $app->db->term()
            ->select('termCode,termStartDate,termEndDate')
            ->where('termCode = ?', $_POST['termCode'])->_and_()
            ->where('active = "1"');
        $q = $term->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        foreach ($q as $k => $v) {
            $json = array('input#startDate' => $v['termStartDate'], 'input#endDate' => $v['termEndDate']);
        }
        echo json_encode($json);
    });

    $app->post('/stuLookup/', function() use($app) {
        $stu = $app->db->student()->where('stuID = ?', (int) $_POST['stuID'])->findOne();
        $nae = $app->db->person()->where('personID = ?', (int) $stu->stuID)->findOne();

        $json = [ 'input#stuName' => $nae->lname . ', ' . $nae->fname];

        echo json_encode($json);
    });

    $app->get('/regTermLookup/', function() use($app) {

        $term = $app->db->query("SELECT termCode,termName FROM term WHERE termCode <> 'NULL' AND active ='1'");
        $q = $term->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $items = [];
        foreach ($q as $r) {
            $option = [ 'id' => $r['termCode'], 'value' => $r['termName']];
            $items[] = $option;
        }

        $data = json_encode($items);
        $response = isset($_GET['callback']) ? $_GET['callback'] . "(" . $data . ")" : $data;
        echo($response);
    });

    $app->get('/regSecLookup/', function() use($app) {

        // Get parameters from Array
        $id = !empty($_GET['id']) ? $_GET['id'] : '';
        $sect = $app->db->query("SELECT courseSecID,courseSection FROM course_sec WHERE termCode = ? AND currStatus = 'A'", [$id]);

        $q = $sect->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $items = [];
        foreach ($q as $r) {
            $option = [ 'id' => $r['courseSecID'], 'value' => $r['courseSection']];
            $items[] = $option;
        }

        $data = json_encode($items);
        $response = isset($_GET['callback']) ? $_GET['callback'] . "(" . $data . ")" : $data;
        echo($response);
    });

    $app->get('/defSecLookup/', function() use($app) {

        // Get parameters from Array
        $term = !empty($_GET['term']) ? $_GET['term'] : '';
        $sect = $app->db->course_sec()
            ->select('DISTINCT course_sec.courseSecID,course_sec.courseSecCode,course_sec.termCode,course_sec.courseSection')
            ->_join('stu_course_sec', 'course_sec.courseSecID = b.courseSecID', 'b')
            ->where('course_sec.termCode = ?', $term)->_and_()
            ->where('course_sec.currStatus = "A"')->_and_()
            ->whereNotNull('b.stuID');

        $q = $sect->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $items = [];
        foreach ($q as $r) {
            $option = [ 'id' => $r['courseSecID'], 'value' => $r['courseSection']];
            $items[] = $option;
        }

        $data = json_encode($items);
        $response = isset($_GET['callback']) ? $_GET['callback'] . "(" . $data . ")" : $data;
        echo($response);
    });

    $app->post('/loc/', function() use($app) {
        $loc = $app->db->location();
        foreach ($_POST as $k => $v) {
            $loc->$k = $v;
        }
        $loc->save();
        $ID = $loc->lastInsertId();

        $location = $app->db->location()
            ->where('locationID = ?', $ID);
        $q = $location->find(function($data) {
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

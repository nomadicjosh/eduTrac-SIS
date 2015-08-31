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
        redirect(url('/lock/'));
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

$json_url = url('/api/');

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
            redirect(url('/dashboard/'));
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
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {
        $json = _file_get_contents($json_url . 'course_sec/courseSecID/' . (int) $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);

        $date = date("Y-m-d");
        $time = date("h:i A");

        if ($app->req->isPost()) {
            $term = str_replace("/", "", $_POST['termCode']);

            $sect = $app->db->course_sec();
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

            $da = $app->db->term()->where('termCode = ?', $decode[0]['termCode'])->findOne();

            if ($decode[0]['currStatus'] != $_POST['currStatus']) {
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
                $logger->setLog('Update Record', 'Course Section', $_POST['secShortTitle'] . ' (' . $_POST['termCode'] . '-' . $decode[0]['courseSecCode'] . ')', get_persondata('uname'));
                $app->flash('success_message', $flashNow->notice(200));
            } else {
                $logger->setLog('Update Error', 'Course Section', $_POST['secShortTitle'] . ' (' . $_POST['termCode'] . '-' . $decode[0]['courseSecCode'] . ')', get_persondata('uname'));
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect(url('/sect/') . $id . '/');
        }

        $preReq = $app->db->course()->select('preReq')->where('courseID = ?', $decode[0]['courseID']);
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
         */ elseif (count($decode[0]['courseSecID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/view', [
                'title' => $decode[0]['secShortTitle'] . ' :: Course Section',
                'cssArray' => $css,
                'jsArray' => $js,
                'sect' => $decode,
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
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        $json = _file_get_contents($json_url . 'course/courseID/' . (int) $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);

        if ($app->req->isPost()) {
            $sc = $decode[0]['courseCode'] . '-' . $_POST['sectionNumber'];
            $courseSection = $_POST['termCode'] . '-' . $decode[0]['courseCode'] . '-' . $_POST['sectionNumber'];

            $dotw = '';
            /** Combine the days of the week to be entered into the database */
            $days = $_POST['dotw'];
            for ($i = 0; $i < sizeof($days); $i++) {
                $dotw .= $days[$i];
            }

            $sect = $app->db->course_sec();
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
            $sect->courseCode = $decode[0]['courseCode'];
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
                    "approvedDate" => $_POST['approvedDate'], "approvedBy" => $_POST['approvedBy'], "secLongTitle" => $decode[0]['courseLongTitle'],
                    "section" => _trim($courseSection), "description" => $decode[0]['courseDesc']
                ];
                /**
                 * Create Course Section Action Hook
                 * 
                 * Fired when a course section is created.
                 * 
                 * @param mixed $section An array of values
                 * @return mixed
                 */
                do_action('create_course_section', $section);
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Course Section', _trim($courseSection), get_persondata('uname'));
                redirect(url('/sect/') . $ID . '/');
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
         */ elseif (count($decode[0]['courseID']) <= 0) {

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
                'sect' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addnl/(\d+)/', function() {
        if (!hasPermission('access_course_sec_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/addnl/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        $json = _file_get_contents($json_url . 'course_sec/courseSecID/' . (int) $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);

        if ($app->req->isPost()) {
            $sect = $app->db->course_sec();
            foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                $sect->$k = $v;
            }
            $sect->where('courseSecID = ?', $id);
            if ($sect->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Course Section', $decode[0]['courseSection'], get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect($app->req->server['HTTP_REFERER']);
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
         */ elseif (count($decode[0]['courseSecID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/addnl-info', [
                'title' => $decode[0]['secShortTitle'] . ' :: Course Section',
                'cssArray' => $css,
                'jsArray' => $js,
                'sect' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/soff/(\d+)/', function() {
        if (!hasPermission('access_course_sec_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/soff/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {
        $json = _file_get_contents($json_url . 'course_sec/courseSecID/' . (int) $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);

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
                $logger->setLog('Update Record', 'Course Section Offering', $decode[0]['courseSection'], get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect(url('/sect/soff/') . (int) $id . '/');
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
         */ elseif (count($decode[0]['courseSecID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/offering-info', [
                'title' => $decode[0]['secShortTitle'] . ' :: Course Section',
                'cssArray' => $css,
                'jsArray' => $js,
                'sect' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/fgrade/(\d+)/', function() {
        if (!hasPermission('access_grading_screen')) {
            redirect(url('/dashboard/'));
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

        $grade = $app->db->course_sec()
            ->select('course_sec.courseSecID,course_sec.secShortTitle,course_sec.minCredit,course_sec.courseSection,course_sec.facID')
            ->select('b.stuID,b.courseSecCode,b.courseSection,b.termCode,b.grade')
            ->_join('stu_acad_cred', 'course_sec.courseSecID = b.courseSecID', 'b')
            ->where('courseSecID = ?', $id);
        $q = $grade->find(function($data) {
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
                'sect' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/rgn/', function() {
        if (!hasPermission('register_students')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/rgn/', function () use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {

        $time = date("h:i A");

        if ($app->req->isPost()) {
            $json_sect = _file_get_contents($json_url . 'course_sec/courseSecID/' . (int) $_POST['courseSecID'] . '/?key=' . get_option('api_key'));
            $sect = json_decode($json_sect, true);

            $json_crse = _file_get_contents($json_url . 'course/courseID/' . (int) $sect[0]['courseID'] . '/?key=' . get_option('api_key'));
            $crse = json_decode($json_crse, true);

            $json_term = _file_get_contents($json_url . 'term/termCode/' . $sect[0]['termCode'] . '/?key=' . get_option('api_key'));
            $term = json_decode($json_term, true);

            $stcs = $app->db->stu_course_sec();
            $stcs->stuID = $_POST['stuID'];
            $stcs->courseSecID = $sect[0]['courseSecID'];
            $stcs->courseSecCode = $sect[0]['courseSecCode'];
            $stcs->courseSection = $sect[0]['courseSection'];
            $stcs->termCode = $sect[0]['termCode'];
            $stcs->courseCredits = $sect[0]['minCredit'];
            $stcs->ceu = $sect[0]['ceu'];
            $stcs->status = 'A';
            $stcs->regDate = $app->db->NOW();
            $stcs->regTime = date("h:i A");
            $stcs->statusDate = $app->db->NOW();
            $stcs->statusTime = $time;
            $stcs->addedBy = get_persondata('personID');

            $stac = $app->db->stu_acad_cred();
            $stac->stuID = $_POST['stuID'];
            $stac->courseID = $sect[0]['courseID'];
            $stac->courseSecID = $sect[0]['courseSecID'];
            $stac->courseCode = $sect[0]['courseCode'];
            $stac->courseSecCode = $sect[0]['courseSecCode'];
            $stac->sectionNumber = $sect[0]['sectionNumber'];
            $stac->courseSection = $sect[0]['courseSection'];
            $stac->termCode = $sect[0]['termCode'];
            $stac->reportingTerm = $term[0]['reportingTerm'];
            $stac->subjectCode = $crse[0]['subjectCode'];
            $stac->deptCode = $sect[0]['deptCode'];
            $stac->shortTitle = $crse[0]['courseShortTitle'];
            $stac->longTitle = $crse[0]['courseLongTitle'];
            $stac->attCred = $sect[0]['minCredit'];
            $stac->status = 'A';
            $stac->statusDate = $app->db->NOW();
            $stac->statusTime = $time;
            $stac->acadLevelCode = $sect[0]['acadLevelCode'];
            $stac->courseLevelCode = $sect[0]['courseLevelCode'];
            $stac->startDate = $sect[0]['startDate'];
            $stac->endDate = $sect[0]['endDate'];
            $stac->addedBy = get_persondata('personID');
            $stac->addDate = $app->db->NOW();

            /**
             * Fires when a staff member registers a student
             * into a course.
             * 
             * @since 6.1.00
             * @return mixed
             */
            do_action('rgn_student_course_registration');

            if ($stcs->save() && $stac->save()) {
                if (function_exists('financial_module')) {
                    /**
                     * Generate bill and/or add fees.
                     */
                    generate_stu_bill($sect[0]['termCode'], $_POST['stuID'], $sect[0]['courseSecID']);
                }
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Course Registration Via Staff', get_name($_POST['stuID']) . ' - ' . $sect[0]['secShortTitle'], get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect(url('/sect/rgn/'));
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
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/sros/', function () use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            redirect(url('/sect/sros/') . $_POST['sectionID'] . '/' . $_POST['template'] . '/');
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
            redirect(url('/dashboard/'));
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

    $app->post('/stuLookup/', function() use($json_url) {
        $json_stu = _file_get_contents($json_url . 'student/stuID/' . (int) $_POST['stuID'] . '/?key=' . get_option('api_key'));
        $stu = json_decode($json_stu, true);

        $json_per = _file_get_contents($json_url . 'person/personID/' . (int) $stu[0]['stuID'] . '/?key=' . get_option('api_key'));
        $per = json_decode($json_per, true);

        $json = [ 'input#stuName' => $per[0]['lname'] . ', ' . $per[0]['fname']];

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

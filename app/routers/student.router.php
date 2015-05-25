<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

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

$app->group('/stu', function() use ($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/', function() {
        if (!hasPermission('access_student_screen')) {
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

    $app->match('GET|POST', '/', function () use($app, $css, $js) {

        $post = $_POST['spro'];

        $spro = $app->db->student()
            ->setTableAlias('a')
            ->select('a.stuID,b.lname,b.fname,b.email')
            ->_join('person', 'a.stuID = b.personID', 'b')
            ->whereLike('CONCAT(b.fname," ",b.lname)', "%$post%")->_or_()
            ->whereLike('CONCAT(b.lname," ",b.fname)', "%$post%")->_or_()
            ->whereLike('CONCAT(b.lname,", ",b.fname)', "%$post%")->_or_()
            ->whereLike('b.uname', "%$post%")->_or_()
            ->whereLike('a.stuID', "%$post%");

        $q = $spro->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('student/index', [
            'title' => 'Student Search',
            'cssArray' => $css,
            'jsArray' => $js,
            'search' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_student_screen')) {
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

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        if ($app->req->isPost()) {
            $spro = $app->db->student();
            foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                $spro->$k = $v;
            }
            $spro->where('stuID = ?', $id);
            if ($spro->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Student Profile (SPRO)', get_name($id), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $spro = $app->db->student()->where('stuID', $id)->findOne();
        $json = _file_get_contents($json_url . 'application/personID/' . (int) $id . '/?key=' . $app->hook->{'get_option'}('api_key'));
        $admit = json_decode($json, true);

        $prog = $app->db->query("SELECT 
                    a.stuProgID,a.stuID,a.acadProgCode,a.currStatus,
                    a.statusDate,a.startDate,a.approvedBy,b.acadLevelCode AS progAcadLevel,
                    b.locationCode,
                    CASE c.status 
                    WHEN 'A' Then 'Active' 
                    ELSE 'Inactive' 
                    END AS 'stuStatus' 
                FROM stu_program a 
                LEFT JOIN acad_program b ON a.acadProgCode = b.acadProgCode 
                LEFT JOIN student c ON a.stuID = c.stuID 
                WHERE a.stuID = ? 
                ORDER BY a.statusDate", [$id]
        );

        $q = $prog->find(function($data) {
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
        if ($spro == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($spro) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($spro) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/view', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'prog' => $q,
                'admit' => $admit
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/add/(\d+)/', function() {
        if (!hasPermission('create_stu_record')) {
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

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {

        if ($app->req->isPost()) {
            $student = $app->db->student();
            $student->stuID = $id;
            $student->status = $_POST['status'];
            $student->addDate = $app->db->NOW();
            $student->approvedBy = get_persondata('personID');

            $sacp = $app->db->stu_program();
            $sacp->stuID = $id;
            $sacp->acadProgCode = _trim($_POST['acadProgCode']);
            $sacp->currStatus = 'A';
            $sacp->statusDate = $app->db->NOW();
            $sacp->startDate = $_POST['startDate'];
            $sacp->approvedBy = get_persondata('personID');
            $sacp->antGradDate = $_POST['antGradDate'];
            $sacp->advisorID = $_POST['advisorID'];
            $sacp->catYearCode = _trim($_POST['catYearCode']);

            $al = $app->db->stu_acad_level();
            $al->stuID = $id;
            $al->acadProgCode = _trim($_POST['acadProgCode']);
            $al->acadLevelCode = _trim($_POST['acadLevelCode']);
            $al->addDate = $app->db->NOW();

            if ($student->save() && $sacp->save() && $al->save()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Student', get_name($id), get_persondata('uname'));
                redirect(url('/') . 'stu/' . $id . '/' . bm());
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                $app->req->server['HTTP_REFERER'];
            }
        }

        $stu = $app->db->acad_program()
            ->setTableAlias('a')
            ->select('a.acadProgID,a.acadProgCode,a.acadProgTitle')
            ->select('a.acadLevelCode,b.majorName,c.locationName')
            ->select('d.schoolName,e.personID,e.startTerm')
            ->_join('major', 'a.majorCode = b.majorCode', 'b')
            ->_join('location', 'a.locationCode = c.locationCode', 'c')
            ->_join('school', 'a.schoolCode = d.schoolCode', 'd')
            ->_join('application', 'a.acadProgCode = e.acadProgCode', 'e')
            ->_join('student', 'e.personID = f.stuID', 'f')
            ->where('e.personID = ?', $id)->_and_()
            ->whereNull('f.stuID');

        $q = $stu->find(function($data) {
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
        if ($q === false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, redirect to the current
         * student record.
         */ elseif (count($q) <= 0) {

            redirect(url('/stu/') . $id . '/');
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/add', [
                'title' => 'Create Student Record',
                'cssArray' => $css,
                'jsArray' => $js,
                'student' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/stac/(\d+)/', function() {
        if (!hasPermission('access_student_screen')) {
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

    $app->match('GET|POST', '/stac/(\d+)/', function ($id) use($app, $css, $js, $json_url) {

        $stac = $app->db->stu_acad_cred()
            ->select('stuAcadCredID,stuID,attCred,ceu')
            ->select('status,termCode,courseCode')
            ->select('shortTitle,grade,courseSection')
            ->where('stuID = ?', $id)
            ->groupBy('courseCode,termCode');

        $q = $stac->find(function($data) {
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
        if ($q === false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (get_name($id) === ", ") {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/stac', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'stac' => $q,
                'stu' => $id
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/sttr/(\d+)/', function() {
        if (!hasPermission('access_student_screen')) {
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

    $app->match('GET|POST', '/sttr/(\d+)/', function ($id) use($app, $css, $js, $json_url) {

        $sttr = $app->db->stu_term_gpa()
            ->setTableAlias('a')
            ->select('a.termCode,a.acadLevelCode,a.attCred,a.compCred')
            ->select('a.stuID,a.gradePoints,a.termGPA,a.acadLevelCode')
            ->select('b.termStartDate,b.termEndDate,d.stuLoad')
            ->_join('term', 'a.termCode = b.termCode', 'b')
            ->_join('stu_course_sec', 'a.termCode = c.termCode AND a.stuID = c.stuID', 'c')
            ->_join('stu_term_load', 'a.termCode = d.termCode AND a.stuID = d.stuID', 'd')
            ->where('a.stuID = ?', $id)
            ->groupBy('a.termCode, a.stuID')
            ->orderBy('a.termCode', 'ASC');

        $q = $sttr->find(function($data) {
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
        if ($q === false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (get_name($id) === ", ") {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/sttr', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'sttr' => $q,
                'stu' => $id
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/shis/(\d+)/', function() {
        if (!hasPermission('access_student_screen')) {
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

    $app->match('GET|POST', '/shis/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {

        if ($app->req->isPost()) {
            if (isset($_POST['shisID'])) {
                $size = count($_POST['shisID']);
                $i = 0;
                while ($i < $size) {
                    $shis = $app->db->hiatus();
                    $shis->shisCode = $_POST['shisCode'][$i];
                    $shis->startDate = $_POST['startDate'][$i];
                    $shis->endDate = $_POST['endDate'][$i];
                    $shis->comment = $_POST['comment'][$i];
                    $shis->where('stuID = ?', $id)->_and_()->where('shisID = ?', $_POST['shisID'][$i]);
                    if ($shis->update()) {
                        $dbcache->clearCache("hiatus-" . $_POST['shisID'][$i]);
                        $app->flash('success_message', $flashNow->notice(200));
                        $logger->setLog('Update Record', 'Student Hiatus', get_name($id), get_persondata('uname'));
                    } else {
                        $app->flash('error_message', $flashNow->notice(409));
                    }
                    ++$i;
                }
            } else {
                $shis = $app->db->hiatus();
                foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                    $shis->$k = $v;
                }
                if ($shis->save()) {
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Student Hiatus (SHIS)', get_name($id), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $json = _file_get_contents($json_url . 'student/stuID/' . $id . '/?key=' . $app->hook->{'get_option'}('api_key'));
        $decode = json_decode($json, true);

        $shis = $app->db->query("SELECT 
                    CASE shisCode 
                	WHEN 'W' THEN 'Withdrawal'
                	WHEN 'LOA' THEN 'Leave of Absence'
                	WHEN 'SA' THEN 'Study Abroad'
                	WHEN 'ILL' THEN 'Illness'
                	ELSE 'Dismissed'
                	END AS 'Code',
                	shisID,stuID,shisCode,startDate,endDate,comment 
                    FROM hiatus 
                    WHERE stuID = ? 
                    ORDER BY shisID DESC", [$id]
        );

        $q = $shis->find(function($data) {
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
         */ elseif (count($decode[0]['stuID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/shis', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'shis' => $q,
                'stu' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/strc/(\d+)/', function() {
        if (!hasPermission('access_student_screen')) {
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

    $app->match('GET|POST', '/strc/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {

        if ($app->req->isPost()) {
            if (isset($_POST['rstrID'])) {
                $size = count($_POST['rstrID']);
                $i = 0;
                while ($i < $size) {
                    $strc = $app->db->restriction();
                    $strc->rstrCode = $_POST['rstrCode'][$i];
                    $strc->severity = $_POST['severity'][$i];
                    $strc->startDate = $_POST['startDate'][$i];
                    $strc->endDate = $_POST['endDate'][$i];
                    $strc->comment = $_POST['comment'][$i];
                    $strc->where('stuID = ?', $id)->_and_()->where('rstrID = ?', $_POST['rstrID'][$i]);
                    if ($strc->update()) {
                        $dbcache->clearCache("restriction-" . $_POST['rstrID'][$i]);
                        $app->flash('success_message', $flashNow->notice(200));
                        $logger->setLog('Update Record', 'Student Restriction', get_name($id), get_persondata('uname'));
                    } else {
                        $app->flash('error_message', $flashNow->notice(409));
                    }
                    ++$i;
                }
            } else {
                $strc = $app->db->restriction();
                foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                    $strc->$k = $v;
                }
                if ($strc->save()) {
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Student Restriction (STRC)', get_name($id), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $json = _file_get_contents($json_url . 'student/stuID/' . $id . '/?key=' . $app->hook->{'get_option'}('api_key'));
        $decode = json_decode($json, true);

        $strc = $app->db->query("SELECT 
                        a.*,b.deptCode 
                    FROM restriction a 
                    LEFT JOIN restriction_code b ON a.rstrCode = b.rstrCode 
                    WHERE a.stuID = ? 
                    ORDER BY a.rstrID", [$id]
        );

        $q = $strc->find(function($data) {
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
         */ elseif (count($decode[0]['stuID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/strc', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'strc' => $q,
                'stu' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/sacd/(\d+)/', function() {
        if (!hasPermission('access_student_screen')) {
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

    $app->match('GET|POST', '/sacd/(\d+)/', function ($id) use($app, $css, $js, $json_url, $dbcache) {

        $json = _file_get_contents($json_url . 'stu_acad_cred/stuAcadCredID/' . (int) $id . '/?key=' . $app->hook->{'get_option'}('api_key'));
        $decode = json_decode($json, true);

        $date = date("Y-m-d");
        $time = date("h:m A");

        if ($app->req->isPost()) {
            $rterm = _file_get_contents($json_url . 'term/termCode/' . $_POST['termCode'] . '/?key=' . $app->hook->{'get_option'}('api_key'));
            $term = json_decode($rterm, true);

            $sacd = $app->db->stu_acad_cred();
            $sacd->courseID = $_POST['courseID'];
            $sacd->courseSecID = $_POST['courseSecID'];
            $sacd->courseCode = $_POST['courseCode'];
            $sacd->courseSecCode = $_POST['courseSecCode'];
            $sacd->sectionNumber = $_POST['sectionNumber'];
            $sacd->courseSection = $_POST['courseSection'];
            $sacd->termCode = $_POST['termCode'];
            $sacd->reportingTerm = $term[0]['reportingTerm'];
            $sacd->subjectCode = $_POST['subjectCode'];
            $sacd->deptCode = $_POST['deptCode'];
            $sacd->shortTitle = $_POST['shortTitle'];
            $sacd->longTitle = $_POST['longTitle'];
            $sacd->attCred = $_POST['attCred'];
            $sacd->ceu = $_POST['ceu'];
            $sacd->status = $_POST['status'];
            $sacd->statusTime = $_POST['statusTime'];
            $sacd->acadLevelCode = $_POST['acadLevelCode'];
            $sacd->courseLevelCode = $_POST['courseLevelCode'];
            $sacd->creditType = $_POST['creditType'];
            $sacd->startDate = $_POST['startDate'];
            $sacd->endDate = $_POST['endDate'];
            if (($_POST['status'] == 'W' || $_POST['status'] == 'D') && $date >= $term[0]['termStartDate'] && $date > $term[0]['dropAddEndDate']) {
                $sacd->compCred = '0.0';
                $sacd->gradePoints = acadCredGradePoints($_POST['grade'], '0.0');
                if (empty($_POST['grade'])) {
                    $sacd->grade = "W";
                } else {
                    $sacd->grade = $_POST['grade'];
                }
            } else {
                $sacd->compCred = $_POST['compCred'];
                $sacd->gradePoints = acadCredGradePoints($_POST['grade'], $_POST['compCred']);
                $sacd->grade = $_POST['grade'];
            }
            $sacd->where('stuAcadCredID = ?', $id);

            /**
             * If the posted status is 'W' or 'D' and today's date is less than the 
             * primary term start date, then delete all student course sec as well as 
             * student acad cred records.
             */
            if (($_POST['status'] == 'W' || $_POST['status'] == 'D') && $date < $term[0]['termStartDate']) {
                $q = $app->db->stu_course_sec()
                    ->where('stuID = ?', $_POST['stuID'])->_and_()
                    ->where('courseSecID = ?', $_POST['courseSecID'])
                    ->delete();
                $q = $app->db->stu_acad_cred()->where('stuAcadCredID = ?', $id)->delete();
                redirect(url('/') . 'stu/stac/' . $_POST['stuID'] . '/' . bm());
                exit();
            }
            /**
             * If posted status is 'W' or 'D' and today's date is greater than equal to the 
             * primary term start date, and today's date is less than the term's drop/add 
             * end date, then delete all student course sec as well as student acad cred 
             * records.
             */ elseif (($_POST['status'] == 'W' || $_POST['status'] == 'D') && $date >= $term[0]['termStartDate'] && $date < $term[0]['dropAddEndDate']) {
                $q = $app->db->stu_course_sec()
                    ->where('stuID = ?', $_POST['stuID'])->_and_()
                    ->where('courseSecID = ?', $_POST['courseSecID'])
                    ->delete();
                $q = $app->db->stu_acad_cred()->where('stuAcadCredID = ?', $id)->delete();
                redirect(url('/') . 'stu/stac/' . $_POST['stuID'] . '/' . bm());
                exit();
            }
            /**
             * If posted status is 'W' or 'D' and today's date is greater than equal to the 
             * primary term start date, and today's date is greater than the term's drop/add 
             * end date, then update student course sec record with a 'W' status and update  
             * student acad record with a 'W' grade and 0.0 completed credits.
             */ elseif (($_POST['status'] == 'W' || $_POST['status'] == 'D') && $date >= $term[0]['termStartDate'] && $date > $term[0]['dropAddEndDate']) {
                $q = $app->db->stu_course_sec();
                $q->courseSecCode = $_POST['courseSecCode'];
                $q->termCode = $_POST['termCode'];
                $q->courseCredits = $_POST['attCred'];
                $q->status = $_POST['status'];
                $q->statusDate = $q->NOW();
                $q->statusTime = $time;
                $q->where('stuID = ?', $_POST['stuID'])->_and_()
                    ->where('courseSecID = ?', $_POST['courseSecID'])
                    ->update();
                $sacd->update();
            }
            /**
             * If there is no status change or the status change is not a 'W', 
             * just update stu_course_sec and stu_acad_cred records with the 
             * changed information.
             */ else {
                $q = $app->db->stu_course_sec();
                $q->courseSecCode = $_POST['courseSecCode'];
                $q->termCode = $_POST['termCode'];
                $q->courseCredits = $_POST['attCred'];
                $q->status = $_POST['status'];
                $q->statusDate = $_POST['statusDate'];
                $q->statusTime = $_POST['statusTime'];
                $q->where('stuID = ?', $_POST['stuID'])->_and_()
                    ->where('courseSecID = ?', $_POST['courseSecID'])
                    ->update();
                $sacd->update();
            }
            $dbcache->clearCache("stu_acad_cred-$id");
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
         */ elseif (count($decode) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/sacd', [
                'title' => get_name($decode[0]['stuID']),
                'cssArray' => $css,
                'jsArray' => $js,
                'sacd' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/sacp/(\d+)/', function() {
        if (!hasPermission('access_student_screen')) {
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

    $app->match('GET|POST', '/sacp/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        if ($app->req->isPost()) {
            $sacp = $app->db->stu_program();
            foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                $sacp->$k = $v;
            }
            $sacp->where('stuProgID = ?', $id);
            if ($sacp->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Student Acad Program (SACP)', get_name($id), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect($app->req->server['HTTP_REFERER']);
        }
        $sacp = $app->db->acad_program()
            ->setTableAlias('a')
            ->select('a.acadProgCode,a.schoolCode,a.acadLevelCode,b.stuProgID')
            ->select('b.eligible_to_graduate,b.graduationDate,b.antGradDate')
            ->select('b.stuID,b.advisorID,b.catYearCode,b.currStatus')
            ->select('b.statusDate,b.startDate,b.endDate,b.comments')
            ->select('b.approvedBy,b.LastUpdate,c.schoolName')
            ->_join('stu_program', 'a.acadProgCode = b.acadProgCode', 'b')
            ->_join('school', 'a.schoolCode = c.schoolCode', 'c')
            ->where('b.stuProgID = ?', $id);
        $q = $sacp->find(function($data) {
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
        if ($q === false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) === true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($q) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/sacp', [
                'title' => get_name($q[0]['stuID']),
                'cssArray' => $css,
                'jsArray' => $js,
                'sacp' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/add-prog/(\d+)/', function() {
        if (!hasPermission('create_stu_record')) {
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

    $app->match('GET|POST', '/add-prog/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        if ($app->req->isPost()) {
            $json = _file_get_contents($json_url . 'acad_program/acadProgCode/' . $_POST['acadProgCode'] . '/?key=' . $app->hook->{'get_option'}('api_key'));
            $decode = json_decode($json, true);

            $level = $app->db->stu_acad_level()
                ->where('stuID = ?', $id)->_and_()
                ->where('acadProgCode = ?', $_POST['acadProgCode']);
            $sql = $level->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $sacp = $app->db->stu_program();
            $sacp->stuID = $id;
            $sacp->acadProgCode = _trim($_POST['acadProgCode']);
            $sacp->currStatus = $_POST['currStatus'];
            $sacp->statusDate = $app->db->NOW();
            $sacp->startDate = $_POST['startDate'];
            $sacp->endDate = $_POST['endDate'];
            $sacp->approvedBy = get_persondata('personID');
            $sacp->antGradDate = $_POST['antGradDate'];
            $sacp->advisorID = $_POST['advisorID'];
            $sacp->catYearCode = $_POST['catYearCode'];
            if ($sacp->save()) {
                if (count($sql[0]['id']) <= 0) {
                    $al = $app->db->stu_acad_level();
                    $al->stuID = $id;
                    $al->acadProgCode = _trim($_POST['acadProgCode']);
                    $al->acadLevelCode = $decode[0]['acadLevelCode'];
                    $al->addDate = $app->db->NOW();
                    $al->save();
                }
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Student Academic Program', get_name($id), get_persondata('uname'));
                redirect(url('/') . 'stu/' . $id . '/' . bm());
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                $app->req->server['HTTP_REFERER'];
            }
        }
        $stu = $app->db->student()->where('stuID = ?', $id);
        $q = $stu->find(function($data) {
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
        if ($q === false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) === true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($q) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/add-prog', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'stu' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/graduation/', function() {
        if (!hasPermission('graduate_students')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/graduation/', function () use($app, $css, $js, $dbcache, $logger, $flashNow) {
        if ($app->req->isPost()) {
            if (!empty($_POST['studentID'])) {
                $grad = $app->db->stu_program();
                $grad->statusDate = $grad->NOW();
                $grad->endDate = $grad->NOW();
                $grad->currStatus = 'G';
                $grad->graduationDate = $_POST['gradDate'];
                $grad->where('stuID = ?', $_POST['studentID'])->_and_()->where('eligible_to_graduate = "1"');
                if ($grad->update()) {
                    $dbcache->purge();
                    $app->flash('success_message', $flashNow->notice(200));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            } else {
                $grad = $app->db->graduation_hold();
                $grad->queryID = $_POST['queryID'];
                $grad->gradDate = $_POST['gradDate'];
                if ($grad->save()) {
                    $dbcache->purge();
                    $logger->setLog('Update Record', 'Graduation', get_name($_POST['stuID']), get_persondata('uname'));
                    $app->flash('success_message', $flashNow->notice(200));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
        }
        $app->view->display('student/graduation', [
            'title' => 'Graduation',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/tran.*', function() {
        if (!hasPermission('generate_transcripts')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/tran/', function () use($app, $css, $js) {
        if ($app->req->isPost()) {
            redirect(url('/stu/tran/') . $_POST['studentID'] . '/' . $_POST['acadLevelCode'] . '/');
        }

        $app->view->display('student/tran', [
            'title' => 'Transcript',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    $app->get('/tran/(\d+)/(\w+)/', function ($id, $level) use($app, $css, $js, $flashNow) {

        $tranInfo = $app->db->stu_acad_cred()
            ->setTableAlias('a')
            ->select('CASE a.acadLevelCode WHEN "UG" THEN "Undergraduate" WHEN "GR" THEN "Graduate" WHEN "Phd" THEN "Doctorate" ELSE "Continuing Education" END AS "Level"')
            ->select('a.stuID,b.address1,b.address2,b.city,b.state')
            ->select('b.zip,c.ssn,c.dob,d.graduationDate,f.degreeCode')
            ->select('f.degreeName,g.majorCode,g.majorName,h.minorCode')
            ->select('h.minorName,i.specCode,i.specName,j.ccdCode,j.ccdName')
            ->_join('address', 'a.stuID = b.personID', 'b')
            ->_join('person', 'a.stuID = c.personID', 'c')
            ->_join('stu_program', 'a.stuID = d.stuID', 'd')
            ->_join('acad_program', 'd.acadProgCode = e.acadProgCode', 'e')
            ->_join('degree', 'e.degreeCode = f.degreeCode', 'f')
            ->_join('major', 'e.majorCode = g.majorCode', 'g')
            ->_join('minor', 'e.minorCode = h.minorCode', 'h')
            ->_join('specialization', 'e.specCode = i.specCode', 'i')
            ->_join('ccd', 'e.ccdCode = j.ccdCode', 'j')
            ->where('a.stuID = ?', $id)->_and_()
            ->where('a.acadLevelCode = ?', $level)->_and_()
            ->where('b.addressStatus = "C"')->_and_()
            ->where('b.addressType = "P"')->_and_()
            ->where('e.acadLevelCode = ?', $level)->_and_()
            ->where('d.currStatus = "A"')->_or_()
            ->where('d.currStatus = "G"');
        $info = $tranInfo->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $tranCourse = $app->db->query("SELECT 
                        compCred,attCred,grade,gradePoints,
                        termCode,creditType,
                        shortTitle,REPLACE(courseCode,'-',' ') AS CourseName,courseSecCode,
                        startDate,endDate 
                    FROM stu_acad_cred 
                    WHERE stuID = ? 
                    AND acadLevelCode = ? 
                    AND creditType = 'I' 
                    GROUP BY courseSecCode,termCode,acadLevelCode
                    ORDER BY endDate ASC", [$id, $level]);
        $course = $tranCourse->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $tranGPA = $app->db->stu_acad_cred()
            ->setTableAlias('stac')
            ->select('SUM(stac.attCred) as Attempted')
            ->select('SUM(stac.compCred) as Completed')
            ->select('SUM(stac.gradePoints) as Points')
            ->select('SUM(stac.gradePoints)/SUM(stac.attCred) as GPA')
            ->where('stac.stuID = ?', $id)->_and_()
            ->where('stac.acadLevelCode = ?', $level)->_and_()
            ->whereNotNull('stac.grade')->_and_()
            ->where('stac.creditType = "I"')
            ->groupBy('stac.stuID');
        $gpa = $tranGPA->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $transferCourse = $app->db->query("SELECT 
                        compCred,attCred,grade,gradePoints,
                        termCode,creditType,
                        shortTitle,REPLACE(courseCode,'-',' ') AS CourseName,courseSecCode 
                    FROM stu_acad_cred  
                    WHERE stuID = ? 
                    AND acadLevelCode = ? 
                    AND creditType = 'TR'", [$id, $level]);
        $transCRSE = $transferCourse->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $transferGPA = $app->db->stu_acad_cred()
            ->setTableAlias('stac')
            ->select('SUM(stac.attCred) as Attempted')
            ->select('SUM(stac.compCred) as Completed')
            ->select('SUM(stac.gradePoints) as Points')
            ->select('SUM(stac.gradePoints)/SUM(stac.attCred) as GPA')
            ->where('stac.stuID = ?', $id)->_and_()
            ->where('stac.acadLevelCode = ?', $level)->_and_()
            ->whereNotNull('stac.grade')->_and_()
            ->where('stac.creditType = "TR"')
            ->groupBy('stac.stuID');
        $transGPA = $transferGPA->find(function($data) {
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
        if ($info == false) {

            $app->flash('error_message', $flashNow->notice(204));
            redirect(url('/stu/tran/'));
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($info) == true) {

            $app->flash('error_message', $flashNow->notice(204));
            redirect(url('/stu/tran/'));
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($info) <= 0) {

            $app->flash('error_message', $flashNow->notice(204));
            redirect(url('/stu/tran/'));
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/transcript', [
                'title' => 'Print Transcript',
                'cssArray' => $css,
                'jsArray' => $js,
                'stuInfo' => $info,
                'courses' => $course,
                'tranGPA' => $gpa,
                'transferGPA' => $transGPA,
                'transferCourses' => $transCRSE
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/timetable/', function() use($app) {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(url('/profile/'));
        }

        if (_h($app->hook->{'get_option'}('enable_myet_portal') == 0) && !hasPermission('edit_myet_css')) {
            redirect(url('/offline/'));
        }
    });

    $app->get('/timetable/', function () use($app) {

        $css = [ 'plugins/fullcalendar/fullcalendar.css', 'css/calendar.css'];
        $js = [ 'plugins/fullcalendar/fullcalendar.js'];

        $app->view->display('student/timetable', [
            'title' => 'Transcript',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/bill/', function() use($app) {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(url('/profile/'));
        }

        if (_h($app->hook->{'get_option'}('enable_myet_portal') == 0) && !hasPermission('edit_myet_css')) {
            redirect(url('/offline/'));
        }
    });

    $app->get('/bill/', function () use($app) {

        $css = [ 'css/admin/module.admin.page.alt.tables.min.css'];
        $js = [
            'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
        ];

        $bill = $app->db->bill()
            ->select('ID,stuID,termCode')
            ->where('stuID = ?', get_persondata('personID'))
            ->groupBy('stuID,termCode');
        $q = $bill->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('student/bill', [
            'title' => 'My Bills',
            'cssArray' => $css,
            'jsArray' => $js,
            'bill' => $q
            ]
        );
    });

    $app->match('GET|POST', '/bill/(\d+)/term/(.*)/', function ($id, $term) use($app) {

        $bill = $app->db->student_fee()
            ->setTableAlias('a')
            ->select('a.ID AS "FeeID",a.stuID,b.name')
            ->select('b.amount,c.ID AS "BillID",c.termCode')
            ->select('c.stu_comments,c.dateTime,d.termName')
            ->_join('billing_table', 'a.feeID = b.ID', 'b')
            ->_join('bill', 'a.stuID = c.stuID', 'c')
            ->_join('term', 'c.termCode = d.termCode', 'd')
            ->where('a.stuID = ?', $id)->_and_()
            ->where('c.termCode = ?', $term)->_and_()
            ->where('a.billID = c.ID')->_and_()
            ->where('a.stuID = ?', get_persondata('personID'));
        $q1 = $bill->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $begin = $app->db->query("SELECT 
                        SUM(b.amount) as sum 
                    FROM student_fee a 
                    LEFT JOIN billing_table b ON a.feeID = b.ID 
                    LEFT JOIN bill c ON a.billID = c.ID 
                    WHERE a.stuID = c.stuID 
                    AND c.stuID = ? 
                    AND c.termCode = ? 
                    GROUP BY c.stuID,c.termCode", [$id, $term]
        );
        $q2 = $begin->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $crseFees = $app->db->query("SELECT 
                        COALESCE(SUM(b.courseFee+b.labFee+b.materialFee),0) AS 'CourseFees' 
                    FROM stu_course_sec a
                    LEFT JOIN course_sec b ON a.courseSecCode = b.courseSecCode 
            		AND a.termCode = b.termCode 
                    WHERE a.stuID = ? 
                    AND a.termCode = ? 
                    AND a.status <> 'C'", [$id, $term]
        );
        $q3 = $crseFees->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $sumPay = $app->db->query("SELECT 
                        SUM(amount) as sum 
                    FROM payment 
                    WHERE stuID = ? 
                    AND termCode = ? 
                    GROUP BY stuID,termCode", [$id, $term]
        );
        $q4 = $sumPay->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $sumRefund = $app->db->query("SELECT 
                        SUM(amount) as sum 
                    FROM refund 
                    WHERE stuID = ? 
                    AND termCode = ? 
                    GROUP BY stuID,termCode", [$id, $term]
        );
        $q5 = $sumRefund->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $payment = $app->db->query("SELECT 
                        a.ID AS 'paymentID',a.amount,a.comment,a.dateTime,c.type 
                    FROM payment a 
                    LEFT JOIN bill b ON a.stuID = b.stuID 
                    LEFT JOIN payment_type c ON a.paymentTypeID = c.ptID 
                    WHERE a.termCode = b.termCode 
                    AND a.stuID = ? 
                    AND a.termCode = ?", [$id, $term]
        );
        $q6 = $payment->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $refund = $app->db->query("SELECT 
                        a.ID AS 'refundID',a.amount,a.comment,a.dateTime 
                    FROM refund a 
                    LEFT JOIN bill b ON a.stuID = b.stuID 
                    WHERE a.termCode = b.termCode 
                    AND a.stuID = ? 
                    AND a.termCode = ?", [$id, $term]
        );
        $q7 = $refund->find(function($data) {
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

            $app->view->display('student/vbill', [
                'title' => $term . ' Bill',
                'bill' => $q1,
                'begin' => money_format('-%n', $q2[0]['sum']),
                'courseFees' => $q3,
                'sumPayments' => $q4[0]['sum'],
                'sumRefund' => $q5[0]['sum'],
                'payment' => $q6,
                'refund' => $q7
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/terms/', function() use($app) {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(url('/profile/'));
        }

        if (_h($app->hook->{'get_option'}('enable_myet_portal') == 0) && !hasPermission('edit_myet_css')) {
            redirect(url('/offline/'));
        }
    });

    $app->get('/terms/', function () use($app) {

        $css = [ 'css/admin/module.admin.page.alt.tables.min.css'];
        $js = [
            'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
        ];
        $terms = $app->db->stu_acad_cred()
            ->setTableAlias('stac')
            ->select('stac.stuID,stac.termCode,COUNT(stac.termCode) AS Courses')
            ->where('stac.stuID = ?', get_persondata('personID'))
            ->groupBy('stac.termCode')
            ->orderBy('stac.termCode', 'DESC');
        $q = $terms->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('student/terms', [
            'title' => 'Registered Terms',
            'cssArray' => $css,
            'jsArray' => $js,
            'term' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/schedule.*', function() use($app) {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(url('/profile/'));
        }

        if (_h($app->hook->{'get_option'}('enable_myet_portal') == 0) && !hasPermission('edit_myet_css')) {
            redirect(url('/offline/'));
        }
    });

    $app->get('/schedule/(.*)/', function ($term) use($app) {

        $css = [ 'css/admin/module.admin.page.alt.tables.min.css'];
        $js = [
            'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
        ];

        $terms = $app->db->course_sec()
            ->setTableAlias('a')
            ->select('a.courseSecID,a.courseSecCode,a.secShortTitle,a.startTime,a.termCode')
            ->select('a.endTime,a.dotw,a.facID,b.buildingName,c.roomNumber,d.stuID,e.stuAcadCredID')
            ->_join('building', 'a.buildingCode = b.buildingCode', 'b')
            ->_join('room', 'a.roomCode = c.roomCode', 'c')
            ->_join('stu_course_sec', 'a.courseSecCode = d.courseSecCode', 'd')
            ->_join('stu_acad_cred', 'd.courseSecID = e.courseSecID', 'e')
            ->where('a.termCode = ?', $term)
            ->where('d.stuID = ?', get_persondata('personID'))
            ->where('d.termCode = ?', $term)
            ->whereIn('d.status', ['A', 'N'])
            ->groupBy('d.stuID,d.termCode,d.courseSecCode')
            ->orderBy('d.termCode', 'DESC');
        $q = $terms->find(function($data) {
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

            $app->view->display('student/schedule', [
                'title' => $term . ' Class Schedule',
                'cssArray' => $css,
                'jsArray' => $js,
                'schedule' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/final-grades/', function() use($app) {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(url('/profile/'));
        }

        if (_h($app->hook->{'get_option'}('enable_myet_portal') == 0) && !hasPermission('edit_myet_css')) {
            redirect(url('/offline/'));
        }
    });

    $app->get('/final-grades/', function () use($app) {

        $css = [ 'css/admin/module.admin.page.alt.tables.min.css'];
        $js = [
            'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
        ];

        $final = $app->db->stu_acad_cred()
            ->setTableAlias('a')
            ->select('a.stuID,a.grade,a.termCode')
            ->select('b.courseSecCode,b.secShortTitle')
            ->_join('course_sec', 'a.courseSecID = b.courseSecID', 'b')
            ->where('a.stuID = ?', get_persondata('personID'))
            ->groupBy('a.termCode,a.courseSecCode')
            ->orderBy('a.termCode', 'DESC');
        $q = $final->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('student/fgrades', [
            'title' => 'Final Grades',
            'cssArray' => $css,
            'jsArray' => $js,
            'grades' => $q
            ]
        );
    });

    $app->get('/deleteSHIS/(\d+)/', function ($id) use($app, $flashNow) {
        $q = $app->db->hiatus()->where('shisID = ?', $id);

        if ($q->delete()) {
            $app->flash('success_message', $flashNow->notice(200));
        } else {
            $app->flash('error_message', $flashNow->notice(409));
        }
        redirect($app->req->server['HTTP_REFERER']);
    });

    $app->get('/deleteSTAC/(\d+)/', function ($id) use($app, $flashNow) {
        $q = $app->db->query("DELETE 
						a.*,b.*,c.* 
						FROM transfer_credit a 
						LEFT JOIN stu_acad_cred b ON a.stuAcadCredID = b.stuAcadCredID  
						LEFT JOIN stu_course_sec c ON b.stuID = c.stuID AND b.courseSecID = c.courseSecID 
						WHERE a.stuAcadCredID = ?", [$id]
        );

        if ($q) {
            $app->flash('success_message', $flashNow->notice(200));
        } else {
            $app->flash('error_message', $flashNow->notice(409));
        }
        redirect($app->req->server['HTTP_REFERER']);
    });

    $app->get('/getEvents/', function () use($app, $css, $js) {
        $meta = $app->db->event_meta()
            ->setTableAlias('a')
            ->select('a.*,b.roomCode,c.buildingCode,e.bgcolor')
            ->_join('room', 'a.roomCode = b.roomCode', 'b')
            ->_join('building', 'b.buildingCode = c.buildingCode', 'c')
            ->_join('event', 'a.eventID = d.eventID', 'd')
            ->_join('event_category', 'd.catID = e.catID', 'e')
            ->_join('stu_acad_cred', 'd.termCode = f.termCode AND d.title = f.courseSecCode', 'f')
            ->where('f.stuID = ?', get_persondata('personID'));
        $q = $meta->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        if (count($q[0]['eventID']) > 0) {
            $events = [];
            foreach ($q as $r) {
                $eventArray['eMID'] = $r['eventMetaID'];
                $eventArray['eID'] = $r['eventID'];
                $eventArray['buildingCode'] = $r['buildingCode'];
                $eventArray['roomCode'] = $r['roomCode'];
                $eventArray['title'] = $r['title'];
                $eventArray['description'] = $r['description'];
                $eventArray['start'] = $r['start'];
                $eventArray['end'] = $r['end'];
                $eventArray['color'] = $r['bgcolor'];
                $events[] = $eventArray;
            }
            echo json_encode($events);
        }
    });

    $app->post('/progLookup/', function () use($app, $flashNow) {
        $prog = $app->db->acad_program()
            ->setTableAlias('a')
            ->select('a.acadProgTitle,a.acadLevelCode,a.schoolCode')
            ->select('b.majorName,c.locationName,d.schoolName')
            ->_join('major', 'a.majorCode = b.majorCode', 'b')
            ->_join('location', 'a.locationCode = c.locationCode', 'c')
            ->_join('school', 'a.schoolCode = d.schoolCode', 'd')
            ->where('a.acadProgCode = ?', $_POST['acadProgCode'])->_and_()
            ->where('a.currStatus = "A"')->_and_()
            ->whereLte('a.endDate', '0000-00-00');
        $q = $prog->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        foreach ($q as $k => $v) {
            $json = [
                '#acadProgTitle' => $v['acadProgTitle'], '#locationName' => $v['locationName'],
                "#majorName" => $v['majorName'], "#schoolName" => $v['schoolCode'] . ' ' . $v['schoolName'],
                "#acadLevelCode" => $v['acadLevelCode']
            ];
        }
        echo json_encode($json);
    });

    $app->setError(function() use($app) {

        $app->view->display('error/404', ['title' => '404 Error']);
    });
});

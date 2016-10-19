<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Student Router
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
    'components/modules/admin/forms/elements/bootstrap-timepicker/assets/lib/js/bootstrap-timepicker.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-timepicker/assets/custom/js/bootstrap-timepicker.init.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/jCombo/jquery.jCombo.min.js'
];

$json_url = get_base_url() . 'api' . '/';
$email = _etsis_email();
$flashNow = new \app\src\Core\etsis_Messages();

$app->group('/stu', function() use ($app, $css, $js, $json_url, $flashNow, $email) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/', function() {
        if (!hasPermission('access_student_screen')) {
            redirect(get_base_url() . 'dashboard' . '/');
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
            redirect(get_base_url() . 'dashboard' . '/');
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

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {
        if ($app->req->isPost()) {
            $spro = $app->db->student();
            foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                $spro->$k = $v;
            }
            $spro->where('stuID = ?', $id);

            /**
             * Triggers before SPRO record is updated.
             *
             * @since 6.1.05
             * @param object $spro Student profile object.
             */
            $app->hook->do_action('pre_update_spro', $spro);

            if ($spro->update()) {
                etsis_cache_delete($id, 'stu');
                /**
                 * Triggers after SPRO record is updated.
                 * 
                 * @since 6.1.05
                 * @param object $spro Student profile data object.
                 * @return mixed
                 */
                $app->hook->do_action('post_update_spro', $spro);
                $app->flash('success_message', $flashNow->notice(200));
                etsis_logger_activity_log_write('Update Record', 'Student Profile (SPRO)', get_name($id), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        $spro = $app->db->student()->where('stuID', $id)->findOne();
        $json = _file_get_contents($json_url . 'application/personID/' . (int) $id . '/?key=' . get_option('api_key'));
        $admit = json_decode($json, true);

        $prog = $app->db->query("SELECT 
                    a.stuProgID,a.stuID,a.acadProgCode,a.currStatus,
                    a.statusDate,a.startDate,a.approvedBy,b.acadLevelCode AS progAcadLevel,
                    b.locationCode,
                    CASE c.status 
                    WHEN 'A' Then 'Active' 
                    ELSE 'Inactive' 
                    END AS 'stuStatus',c.tags 
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
            redirect(get_base_url() . 'dashboard' . '/');
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

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow, $email) {
        if ($app->req->isPost()) {
            $nae = get_person_by('personID', $id);
            if ($nae->ssn > 0) {
                $pass = str_replace('-', '', $nae->ssn);
            } elseif ($nae->dob != '0000-00-00') {
                $pass = str_replace('-', '', $nae->dob);
            } else {
                $pass = 'myaccount';
            }
            $degree = $app->db->acad_program()->where('acadProgCode = ?', _trim($_POST['acadProgCode']))->findOne();
            $appl = $app->db->application()->where('personID = ?', $id)->findOne();

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

            /**
             * Fires before new student record is created.
             *
             * @since 6.1.07
             * @param int $id Student's ID.
             */
            $app->hook->do_action('pre_save_stu', $id);

            if ($student->save() && $sacp->save() && $al->save()) {
                if (_h(get_option('send_acceptance_email')) == 1) {
                    $host = strtolower($_SERVER['SERVER_NAME']);
                    $site = _t('myetSIS :: ') . _h(get_option('institution_name'));
                    $message = _escape(get_option('student_acceptance_letter'));
                    $message = str_replace('#uname#', $nae->uname, $message);
                    $message = str_replace('#fname#', $nae->fname, $message);
                    $message = str_replace('#lname#', $nae->lname, $message);
                    $message = str_replace('#name#', get_name($id), $message);
                    $message = str_replace('#id#', $id, $message);
                    $message = str_replace('#email#', $nae->email, $message);
                    $message = str_replace('#sacp#', _trim($_POST['acadProgCode']), $message);
                    $message = str_replace('#acadlevel#', _trim($_POST['acadLevelCode']), $message);
                    $message = str_replace('#degree#', $degree->degreeCode, $message);
                    $message = str_replace('#startterm#', $appl->startTerm, $message);
                    $message = str_replace('#adminemail#', _h(get_option('system_email')), $message);
                    $message = str_replace('#url#', get_base_url(), $message);
                    $message = str_replace('#helpdesk#', _h(get_option('help_desk')), $message);
                    $message = str_replace('#currentterm#', _h(get_option('current_term_code')), $message);
                    $message = str_replace('#instname#', _h(get_option('institution_name')), $message);
                    $message = str_replace('#mailaddr#', _h(get_option('mailing_address')), $message);

                    $headers = "From: $site <auto-reply@$host>\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $email->etsis_mail(_h(get_option('admissions_email')), _t("Student Acceptance Letter"), $message, $headers);
                }
                /**
                 * @since 6.1.07
                 */
                $spro = $app->db->student()
                    ->setTableAlias('spro')
                    ->select('spro.*, nae.*, addr.*')
                    ->_join('person', 'spro.stuID = nae.personID', 'nae')
                    ->_join('address', 'spro.stuID = addr.personID', 'addr')
                    ->where('spro.stuID = ?', $id)->_and_()
                    ->where('addr.addressType = "P"')->_and_()
                    ->where('addr.addressStatus = "C"')
                    ->findOne();
                /**
                 * Fires after new student record has been created.
                 * 
                 * @since 6.1.07
                 * @param array $spro Student data object.
                 */
                $app->hook->do_action('post_save_stu', $spro);

                $app->flash('success_message', $flashNow->notice(200));
                etsis_logger_activity_log_write('New Record', 'Student', get_name($id), get_persondata('uname'));
                redirect(get_base_url() . 'stu/' . $id . '/' . bm());
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

            redirect(get_base_url() . 'stu' . '/' . $id . '/');
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
            redirect(get_base_url() . 'dashboard' . '/');
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
            redirect(get_base_url() . 'dashboard' . '/');
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
            redirect(get_base_url() . 'dashboard' . '/');
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

    $app->match('GET|POST', '/shis/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {

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
                        $app->flash('success_message', $flashNow->notice(200));
                        etsis_logger_activity_log_write('Update Record', 'Student Hiatus', get_name($id), get_persondata('uname'));
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
                    etsis_logger_activity_log_write('New Record', 'Student Hiatus (SHIS)', get_name($id), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
            }
            etsis_cache_delete($id, 'stu');
            redirect($app->req->server['HTTP_REFERER']);
        }

        $json = _file_get_contents($json_url . 'student/stuID/' . $id . '/?key=' . get_option('api_key'));
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
            redirect(get_base_url() . 'dashboard' . '/');
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

    $app->match('GET|POST', '/strc/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {

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
                        $app->flash('success_message', $flashNow->notice(200));
                        etsis_logger_activity_log_write('Update Record', 'Student Restriction', get_name($id), get_persondata('uname'));
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
                    etsis_logger_activity_log_write('New Record', 'Student Restriction (STRC)', get_name($id), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
            }
            etsis_cache_delete($id, 'stu');
            redirect($app->req->server['HTTP_REFERER']);
        }

        $json = _file_get_contents($json_url . 'student/stuID/' . $id . '/?key=' . get_option('api_key'));
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
            redirect(get_base_url() . 'dashboard' . '/');
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

    $app->match('GET|POST', '/sacd/(\d+)/', function ($id) use($app, $css, $js, $json_url) {

        $json = _file_get_contents($json_url . 'stu_acad_cred/stuAcadCredID/' . (int) $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);

        $date = date("Y-m-d");
        $time = date("h:m A");

        if ($app->req->isPost()) {
            $rterm = _file_get_contents($json_url . 'term/termCode/' . $_POST['termCode'] . '/?key=' . get_option('api_key'));
            $term = json_decode($rterm, true);

            $detail = $app->db->stu_acad_cred();
            $detail->courseID = $_POST['courseID'];
            $detail->courseSecID = $decode[0]['courseSecID'];
            $detail->courseCode = $_POST['courseCode'];
            $detail->courseSecCode = $decode[0]['courseSecCode'];
            $detail->sectionNumber = $_POST['sectionNumber'];
            $detail->courseSection = $decode[0]['courseSection'];
            $detail->termCode = $_POST['termCode'];
            $detail->reportingTerm = $term[0]['reportingTerm'];
            $detail->subjectCode = $_POST['subjectCode'];
            $detail->deptCode = $_POST['deptCode'];
            $detail->shortTitle = $_POST['shortTitle'];
            $detail->longTitle = $_POST['longTitle'];
            $detail->attCred = $_POST['attCred'];
            $detail->ceu = $_POST['ceu'];
            $detail->status = $_POST['status'];
            $detail->acadLevelCode = $_POST['acadLevelCode'];
            $detail->courseLevelCode = $_POST['courseLevelCode'];
            $detail->creditType = $_POST['creditType'];
            $detail->startDate = $_POST['startDate'];
            $detail->endDate = $_POST['endDate'];
            if (($_POST['status'] == 'W' || $_POST['status'] == 'D') && $date >= $term[0]['termStartDate'] && $date > $term[0]['dropAddEndDate']) {
                $detail->compCred = '0.0';
                $detail->gradePoints = acadCredGradePoints($_POST['grade'], '0.0');
                $detail->statusTime = $time;
                if (empty($_POST['grade'])) {
                    $detail->grade = "W";
                } else {
                    $detail->grade = $_POST['grade'];
                }
            } else {
                if (acadCredGradePoints($_POST['grade'], $_POST['attCred']) > 0) {
                    $compCred = $_POST['attCred'];
                } else {
                    $compCred = '0';
                }
                $detail->compCred = $compCred;
                $detail->gradePoints = acadCredGradePoints($_POST['grade'], $_POST['attCred']);
                $detail->grade = $_POST['grade'];
            }
            $detail->where('stuAcadCredID = ?', $id);

            /**
             * If the posted status is 'W' or 'D' and today's date is less than the 
             * primary term start date, then delete all student course sec as well as 
             * student acad cred records.
             */
            if (($_POST['status'] == 'W' || $_POST['status'] == 'D') && $date < $term[0]['termStartDate']) {
                $q = $app->db->stu_course_sec()
                    ->where('stuID = ?', $decode[0]['stuID'])->_and_()
                    ->where('courseSection = ?', $decode[0]['courseSection'])
                    ->delete();
                $q = $app->db->stu_acad_cred()->where('stuAcadCredID = ?', $id)->delete();

                if (function_exists('financial_module')) {
                    $q = $app->db->stu_acct_fee()->where('stuID = ?', $decode[0]['stuID'])->_and_()->where('description = ?', $decode[0]['courseSection'])->delete();
                    /**
                     * Begin Updating tuition totals.
                     */
                    $total = qt('course_sec', 'courseFee', 'courseSection = "' . $decode[0]['courseSection'] . '"') + qt('course_sec', 'labFee', 'courseSection = "' . $decode[0]['courseSection'] . '"') + qt('course_sec', 'materialFee', 'courseSection = "' . $decode[0]['courseSection'] . '"');
                    $stuTuition = $app->db->stu_acct_tuition()->where('stuID = ? AND termCode = ?', [$decode[0]['stuID'], $_POST['termCode']])->findOne();
                    $q = $app->db->stu_acct_tuition();
                    $q->total = bcsub($stuTuition->total, $total);
                    $q->where('stuID = ?', $decode[0]['stuID'])->_and_()->where('termCode = ?', $_POST['termCode'])->update();
                    /**
                     * End updating tuition totals.
                     */
                }

                redirect(get_base_url() . 'stu/stac' . '/' . $decode[0]['stuID'] . '/' . bm());
                return;
            }
            /**
             * If posted status is 'W' or 'D' and today's date is greater than equal to the 
             * primary term start date, and today's date is less than the term's drop/add 
             * end date, then delete all student course sec as well as student acad cred 
             * records.
             */ elseif (($_POST['status'] == 'W' || $_POST['status'] == 'D') && $date >= $term[0]['termStartDate'] && $date < $term[0]['dropAddEndDate']) {
                $q = $app->db->stu_course_sec()
                    ->where('stuID = ?', $decode[0]['stuID'])->_and_()
                    ->where('courseSection = ?', $decode[0]['courseSection'])
                    ->delete();
                $q = $app->db->stu_acad_cred()->where('stuAcadCredID = ?', $id)->delete();

                if (function_exists('financial_module')) {
                    $q = $app->db->stu_acct_fee()->where('stuID = ?', $decode[0]['stuID'])->_and_()->where('description = ?', $decode[0]['courseSection'])->delete();
                    /**
                     * Begin Updating tuition totals.
                     */
                    $total = qt('course_sec', 'courseFee', 'courseSection = "' . $decode[0]['courseSection'] . '"') + qt('course_sec', 'labFee', 'courseSection = "' . $decode[0]['courseSection'] . '"') + qt('course_sec', 'materialFee', 'courseSection = "' . $decode[0]['courseSection'] . '"');
                    $q = $app->db->stu_acct_tuition();
                    $q->total = bcsub($q->total, $total);
                    $q->where('stuID = ?', $decode[0]['stuID'])->_and_()->where('termCode = ?', $_POST['termCode'])->update();
                    /**
                     * End updating tuition totals.
                     */
                }

                redirect(get_base_url() . 'stu/stac' . '/' . $decode[0]['stuID'] . '/' . bm());
                return;
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
                $q->where('stuID = ?', $decode[0]['stuID'])->_and_()
                    ->where('courseSecID = ?', $_POST['courseSecID'])
                    ->update();
                $detail->update();
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
                $q->where('stuID = ?', $decode[0]['stuID'])->_and_()
                    ->where('courseSecID = ?', $_POST['courseSecID'])
                    ->update();
                $detail->update();
            }
            /**
             * @since 6.1.08
             */
            $sacd = $app->db->stu_acad_cred()
                ->setTableAlias('sacd')
                ->select('sacd.*,nae.uname,nae.fname,nae.lname,nae.email')
                ->_join('person', 'sacd.stuID = nae.personID', 'nae')
                ->where('stuAcadCredID = ?', $id)
                ->findOne();
            /**
             * Triggers after SACD record is updated.
             * 
             * @since 6.1.05
             * @param array $sacd Student Academic Credit Detail data object.
             */
            $app->hook->do_action('post_update_sacd', $sacd);
            etsis_cache_delete($id, 'stu');
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
            redirect(get_base_url() . 'dashboard' . '/');
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

    $app->match('GET|POST', '/sacp/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {
        if ($app->req->isPost()) {
            $sacp = $app->db->stu_program();
            foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                $sacp->$k = $v;
            }
            $sacp->where('stuProgID = ?', $id);
            if ($sacp->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                etsis_logger_activity_log_write('Update Record', 'Student Acad Program (SACP)', get_name($_POST['stuID']), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            etsis_cache_delete($id, 'stu');
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
            redirect(get_base_url() . 'dashboard' . '/');
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

    $app->match('GET|POST', '/add-prog/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {
        if ($app->req->isPost()) {
            $json = _file_get_contents($json_url . 'acad_program/acadProgCode/' . $_POST['acadProgCode'] . '/?key=' . get_option('api_key'));
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
                etsis_logger_activity_log_write('New Record', 'Student Academic Program', get_name($id), get_persondata('uname'));
                redirect(get_base_url() . 'stu' . '/' . $id . '/' . bm());
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                $app->req->server['HTTP_REFERER'];
            }
            etsis_cache_delete($id, 'stu');
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
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/graduation/', function () use($app, $css, $js, $flashNow) {
        if ($app->req->isPost()) {
            if (!empty($_POST['studentID'])) {
                $grad = $app->db->stu_program();
                $grad->statusDate = $grad->NOW();
                $grad->endDate = $grad->NOW();
                $grad->currStatus = 'G';
                $grad->graduationDate = $_POST['gradDate'];
                $grad->where('stuID = ?', $_POST['studentID'])->_and_()->where('eligible_to_graduate = "1"');
                if ($grad->update()) {
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
                    etsis_logger_activity_log_write('Update Record', 'Graduation', get_name($_POST['stuID']), get_persondata('uname'));
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
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/tran/', function () use($app, $css, $js) {
        if ($app->req->isPost()) {
            redirect(get_base_url() . 'stu/tran' . '/' . $_POST['stuID'] . '/' . $_POST['acadLevelCode'] . '/' . $_POST['template'] . '/');
        }

        $app->view->display('student/tran', [
            'title' => 'Transcript',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    $app->get('/tran/(\d+)/(\w+)/(\w+)/', function ($id, $level, $template) use($app, $css, $js, $flashNow) {

        $tranInfo = $app->db->stu_acad_cred()
            ->setTableAlias('a')
            ->select('CASE a.acadLevelCode WHEN "UG" THEN "Undergraduate" WHEN "GR" THEN "Graduate" '
                . 'WHEN "Phd" THEN "Doctorate" WHEN "CE" THEN "Continuing Education" WHEN "CTF" THEN "Certificate" '
                . 'WHEN "DIP" THEN "Diploma" WHEN "PR" THEN "Professional" ELSE "Non-Degree" END AS "Level"')
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
            ->where('(d.currStatus = "A" OR d.currStatus = "G")');
        $info = $tranInfo->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $tranCourse = $app->db->query("SELECT 
                        stac.compCred,stac.attCred,stac.grade,stac.gradePoints,
                        stac.termCode,stac.creditType,
                        stac.shortTitle,REPLACE(stac.courseCode,'-',' ') AS CourseName,stac.courseSecCode,
                        stac.startDate,stac.endDate 
                    FROM stu_acad_cred stac
                    LEFT JOIN term ON stac.termCode = term.termCode
                    WHERE stac.stuID = ? 
                    AND stac.acadLevelCode = ? 
                    AND stac.creditType = 'I' 
                    GROUP BY stac.courseSecCode,stac.termCode,stac.acadLevelCode
                    ORDER BY term.termStartDate ASC", [$id, $level]);
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

        /* $stuTerms = $app->db->stu_term_gpa()
          ->setTableAlias('sttr')
          ->select('sttr.termCode,sttr.acadLevelCode,sttr.attCred,sttr.compCred')
          ->select('sttr.gradePoints,sttr.termGPA')
          ->_join('term', 'sttr.termCode = term.termCode')
          ->where('sttr.stuID = ?', $id)->_and_()
          ->where('sttr.acadLevelCode = ?', $level)
          ->groupBy('termCode')
          ->orderBy('termStartDate', 'ASC');
          $stuTermTran = $stuTerms->find(function($data) {
          $array = [];
          foreach ($data as $d) {
          $array[] = $d;
          }
          return $array;
          }); */

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($info == false) {

            $app->flash('error_message', $flashNow->notice(204));
            redirect(get_base_url() . 'stu/tran' . '/');
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($info) == true) {

            $app->flash('error_message', $flashNow->notice(204));
            redirect(get_base_url() . 'stu/tran' . '/');
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($info) <= 0) {

            $app->flash('error_message', $flashNow->notice(204));
            redirect(get_base_url() . 'stu/tran' . '/');
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/templates/transcript/' . $template . '.template', [
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
    $app->before('GET|POST', '/timetable/', function() {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(get_base_url() . 'profile' . '/');
        }

        if (_h(get_option('enable_myet_portal')) == 0 && !hasPermission('edit_myet_css')) {
            redirect(get_base_url() . 'offline' . '/');
        }
    });

    $app->get('/timetable/', function () use($app) {

        $css = [ 'plugins/fullcalendar/fullcalendar.css', 'css/calendar.css'];
        $js = [ 'plugins/fullcalendar/fullcalendar.js'];

        $app->view->display('student/timetable', [
            'title' => 'Timetable',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/terms/', function() {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(get_base_url() . 'profile' . '/');
        }

        if (_h(get_option('enable_myet_portal')) == 0 && !hasPermission('edit_myet_css')) {
            redirect(get_base_url() . 'offline' . '/');
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
    $app->before('GET|POST', '/schedule.*', function() {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(get_base_url() . 'profile' . '/');
        }

        if (_h(get_option('enable_myet_portal')) == 0 && !hasPermission('edit_myet_css')) {
            redirect(get_base_url() . 'offline' . '/');
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
    $app->before('GET|POST', '/final-grades/', function() {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(get_base_url() . 'profile' . '/');
        }

        if (_h(get_option('enable_myet_portal')) == 0 && !hasPermission('edit_myet_css')) {
            redirect(get_base_url() . 'offline' . '/');
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

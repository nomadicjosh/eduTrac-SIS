<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Courses Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
/**
 * Before router check.
 */
$app->before('GET|POST', '/courses(.*)', function() use($app) {

    if (_h(get_option('enable_myet_portal') == 0) && !hasPermission('edit_myet_css')) {
        redirect(url('/offline/'));
    }
});

$css = [ 'css/admin/module.admin.page.alt.form_elements.min.css', 'css/admin/module.admin.page.alt.tables.min.css'];
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

$json_url = url('/api/');

$logger = new \app\src\Log();
$email = new \app\src\Email();
$cache = new \app\src\Cache();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();

$app->group('/courses', function() use ($app, $css, $js, $json_url, $logger, $dbcache, $flashNow, $email) {

    $app->match('GET|POST', '/', function () use($app, $css, $js, $json_url, $flashNow) {
        if ($app->req->isPost()) {
            /**
             * Checks to see how many courses the student has already registered 
             * for the requested semester. If the student has already registered for 
             * courses, then count them and add it to the number they are trying to 
             * register for. If that number is greater than the number_of_courses 
             * restriction, then redirect the student to a error page, otherwise 
             * add the courses to the shopping cart.
             */
            $check = $app->db->stu_course_sec()
                ->where('stuID = ?', get_persondata('personID'))->_and_()
                ->where('termCode = ?', $_POST['regTerm'])->_and_()
                ->whereIn('status', ['A', 'N']);
            $q1 = $check->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            if (bcadd(count($q1[0]['id']), count($_POST['courseSecID'])) > get_option('number_of_courses')) {
                $app->flash('error_message', _t('Your institution has set a course registration limit. You are only allowed to register for <strong>') . get_option('number_of_courses') . _t(' courses</strong> per term.'));
                redirect(url('/') . 'courses/');
                exit();
            }
            /* Retrieve the dropAddEndDate from the registration term. */
            $json_term = _file_get_contents($json_url . 'term/termCode/' . $_POST['termCode'] . '/?key=' . get_option('api_key'));
            $daDate = json_decode($json_term, true);
            $deleteDate = date('Y-m-d', strtotime($daDate[0]['dropAddEndDate'] . ' + 1 days'));

            /* Add courses to the shopping cart. */
            $size = count($_POST['courseSecID']);
            $i = 0;
            while ($i < $size) {
                $q2 = $app->db->stu_rgn_cart();
                $q2->stuID = get_persondata('personID');
                $q2->courseSecID = $_POST['courseSecID'][$i];
                $q2->deleteDate = $deleteDate;
                if ($q2->save()) {
                    $app->flash('success_message', $flashNow->notice(200));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                ++$i;
                redirect(url('/') . 'courses/cart/');
            }
        }
        $terms = get_option('open_terms');
        if (function_exists('create_payment_plan') && isStudent(get_persondata('personID'))) {
            $sect = $app->db->course_sec()
                ->setTableAlias('a')
                ->select('a.courseSecID,a.courseSecCode,a.secShortTitle,a.dotw')
                ->select('a.startTime,a.endTime,a.minCredit,a.termCode')
                ->select('a.courseFee,a.labFee,a.materialFee,a.facID')
                ->select('a.comment,a.courseSection,b.locationName,c.courseDesc')
                ->_join('location', 'a.locationCode = b.locationCode', 'b')
                ->_join('course', 'a.courseID = c.courseID', 'c')
                ->_join('acad_program', 'c.subjectCode = d.specCode AND c.acadLevelCode = d.acadLevelCode', 'd')
                ->_join('stu_program', 'd.acadProgCode = e.acadProgCode', 'e')
                ->where('e.stuID = ?', get_persondata('personID'))->_and_()
                ->where('a.currStatus = "A"')->_and_()
                ->where('a.webReg = "1"')->_and_()
                ->where('a.termCode IN(' . $terms . ')');
        } else {
            $sect = $app->db->course_sec()
                ->setTableAlias('a')
                ->select('a.courseSecID,a.courseSecCode,a.secShortTitle,a.dotw')
                ->select('a.startTime,a.endTime,a.minCredit,a.termCode')
                ->select('a.courseFee,a.labFee,a.materialFee,a.facID')
                ->select('a.comment,a.courseSection,b.locationName,c.courseDesc')
                ->_join('location', 'a.locationCode = b.locationCode', 'b')
                ->_join('course', 'a.courseID = c.courseID', 'c')
                ->where('a.currStatus = "A"')->_and_()
                ->where('a.webReg = "1"')->_and_()
                ->where('a.termCode IN(' . $terms . ')');
        }
        $q = $sect->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $app->view->display('courses/index', [
            'title' => 'Search Courses',
            'cssArray' => $css,
            'jsArray' => $js,
            'sect' => $q
            ]
        );
    });

    $app->get('/cart/', function () use($app, $css, $js) {

        $cart = $app->db->course_sec()
            ->setTableAlias('a')
            ->select('a.courseSecID,a.courseSecCode,a.courseSection,a.secShortTitle,a.dotw')
            ->select('a.startTime,a.endTime,a.minCredit,a.termCode')
            ->select('a.courseFee,a.labFee,a.materialFee,a.facID,a.comment,b.locationName')
            ->_join('location', 'a.locationCode = b.locationCode', 'b')
            ->_join('stu_rgn_cart', 'a.courseSecID = c.courseSecID', 'c')
            ->where('c.stuID = ?', get_persondata('personID'));
        $q = $cart->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('courses/cart', [
            'title' => 'My Shopping Cart',
            'cssArray' => $css,
            'jsArray' => $js,
            'cart' => $q
            ]
        );
    });

    $app->post('/reg/', function () use($app, $css, $js, $flashNow, $email) {

        /**
         * Checks to see how many courses the student has already registered 
         * for the requested semester. If the student has already registered for 
         * courses, then count them and add it to the number they are trying to 
         * register for. If that number is greater than the number_of_courses 
         * restriction, then redirect the student to a error page, otherwise 
         * let the registration go through.
         */
        $check = $app->db->stu_course_sec()
            ->where('stuID = ?', get_persondata('personID'))->_and_()
            ->where('termCode = ?', get_option('registration_term'))->_and_()
            ->whereIn('status', ['A', 'N']);
        $d = $check->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $counts = array_count_values($_POST['regAction']);
        if (bcadd(count($d[0]['id']), $counts['register']) > get_option('number_of_courses')) {
            $app->flash('error_message', _t('Your institution has set a course registration limit. You are only allowed to register for <strong>') . get_option('number_of_courses') . _t(' courses</strong> per term.'));
            redirect($app->req->server['HTTP_REFERER']);
            exit();
        }
        /**
         * If the action selected was 'remove', then remove the record from
         * the student's shopping cart.
         */
        $count = count($_POST['regAction']);
        $t = 0;
        while ($t < $count) {
            if ($_POST['regAction'][$t] == 'remove') {
                $app->db->stu_rgn_cart()
                    ->where('stuID = ?', get_persondata('personID'))->_and_()
                    ->where('courseSecID = ?', $_POST['courseSecID'][$t])
                    ->delete();
            }
            ++$t;
        }
        /**
         * If the action selected was 'register', then query the database for
         * the required information and add new records to stu_course_sec as
         * well as stu_acad_cred.
         */
        $size = count($_POST['regAction']);
        $r = 0;
        while ($r < $size) {
            if ($_POST['regAction'][$r] == 'register') {
                $sect = $app->db->course_sec()
                    ->setTableAlias('a')
                    ->select('a.courseSecCode,a.termCode,a.minCredit')
                    ->select('a.courseID,a.sectionNumber,a.courseSection')
                    ->select('a.startDate,a.endDate,b.deptCode')
                    ->select('b.courseShortTitle,b.courseLongTitle')
                    ->select('b.subjectCode,b.creditType,b.courseCode')
                    ->select('b.acadLevelCode,b.courseLevelCode,c.reportingTerm')
                    ->_join('course', 'a.courseID = b.courseID', 'b')
                    ->_join('term', 'a.termCode = c.termCode', 'c')
                    ->where('a.courseSecID = ?', $_POST['courseSecID'][$r]);
                $sql = $sect->find(function($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });

                $stcs = $app->db->stu_course_sec();
                $stcs->stuID = get_persondata('personID');
                $stcs->courseSecID = $_POST['courseSecID'][$r];
                $stcs->courseSecCode = $sql[0]['courseSecCode'];
                $stcs->courseSection = $sql[0]['courseSection'];
                $stcs->termCode = $sql[0]['termCode'];
                $stcs->courseCredits = $sql[0]['minCredit'];
                $stcs->status = 'N';
                $stcs->regDate = $app->db->NOW();
                $stcs->regTime = date("h:i A");
                $stcs->statusDate = $app->db->NOW();
                $stcs->statusTime = date("h:i A");
                $stcs->addedBy = get_persondata('personID');

                if ($stcs->save()) {
                    $stac = $app->db->stu_acad_cred();
                    $stac->stuID = get_persondata('personID');
                    $stac->courseID = $sql[0]['courseID'];
                    $stac->courseSecID = $_POST['courseSecID'][$r];
                    $stac->courseCode = $sql[0]['courseCode'];
                    $stac->courseSecCode = $sql[0]['courseSecCode'];
                    $stac->sectionNumber = $sql[0]['sectionNumber'];
                    $stac->courseSection = $sql[0]['courseSection'];
                    $stac->termCode = $sql[0]['termCode'];
                    $stac->reportingTerm = $sql[0]['reportingTerm'];
                    $stac->subjectCode = $sql[0]['subjectCode'];
                    $stac->deptCode = $sql[0]['deptCode'];
                    $stac->shortTitle = $sql[0]['courseShortTitle'];
                    $stac->longTitle = $sql[0]['courseLongTitle'];
                    $stac->compCred = '0.0';
                    $stac->attCred = $sql[0]['minCredit'];
                    $stac->status = 'N';
                    $stac->statusDate = $app->db->NOW();
                    $stac->statusTime = date("h:i A");
                    $stac->acadLevelCode = $sql[0]['acadLevelCode'];
                    $stac->courseLevelCode = $sql[0]['courseLevelCode'];
                    $stac->creditType = $sql[0]['creditType'];
                    $stac->startDate = $sql[0]['startDate'];
                    $stac->endDate = $sql[0]['endDate'];
                    $stac->addedBy = get_persondata('personID');
                    $stac->addDate = $app->db->NOW();
                    $stac->save();
                    $ID = $stac->lastInsertId();
                    $now = $app->db->NOW();
                    /**
                     * Is triggered after registration and after new STAC record
                     * is added to the database.
                     * 
                     * @since 6.1.05
                     * @param mixed $stac Array of STAC data.
                     * @param int $ID Primary key of the new STAC record.
                     * @return mixed
                     */
                    do_action_array('post_save_myet_stac', [ $stac, $ID ]);
                    /**
                     * Delete the record from the shopping cart after
                     * registration is complete.
                     */
                    $app->db->stu_rgn_cart()
                        ->where('stuID = ?', get_persondata('personID'))->_and_()
                        ->where('courseSecID = ?', $_POST['courseSecID'][$r])
                        ->delete();

                    if (function_exists('financial_module')) {
                        /**
                         * Generate bill and/or add fees.
                         */
                        generate_stu_bill($sql[0]['termCode'], get_persondata('personID'), $_POST['courseSecID'][$r]);
                    }
                }
            }
            ++$r;
        }

        /**
         * Fires when a student registers for a course.
         * 
         * @since 6.1.00
         * @return mixed
         */
        do_action('myet_student_course_registration');

        // Flash messages for success or error
        if ($ID > 0) {
            $st = $app->db->stu_acad_cred()
                ->select('courseSection')
                ->where('stuID = ?', get_persondata('personID'))->_and_()
                ->where('LastUpdate = ?', $now);
            $qry = $st->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            if (count($qry[0]['courseSection']) > 0) {
                if (get_option('registrar_email_address') != '') {
                    $email->course_registration(get_persondata('personID'), $_POST['termCode'], url('/'));
                }
            }
            $app->flash('success_message', $flashNow->notice(200));
        } else {
            $app->flash('error_message', $flashNow->notice(409));
        }
        redirect($app->req->server['HTTP_REFERER']);
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

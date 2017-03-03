<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

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
$app->before('GET|POST', '/courses(.*)', function() {

    if (get_option('enable_myet_portal') == 0 && !hasPermission('edit_myet_css')) {
        redirect(get_base_url() . 'offline' . '/');
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

$app->group('/courses', function() use ($app, $css, $js) {

    $app->match('GET|POST', '/', function () use($app, $css, $js) {
        if ($app->req->isPost()) {
            try {
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
                    ->where('termCode = ?', $app->req->post['regTerm'])->_and_()
                    ->whereIn('status', ['A', 'N']);
                $q1 = $check->find(function($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                if (bcadd(count($q1[0]['id']), count($app->req->post['courseSecID'])) > get_option('number_of_courses')) {
                    _etsis_flash()->error(sprintf(_t('Your institution has set a course registration limit. You are only allowed to register for <strong>%s courses</strong> per term.'), get_option('number_of_courses')), get_base_url() . 'courses' . '/');
                    exit();
                }
                /* Retrieve the dropAddEndDate from the registration term. */
                $term = $app->db->term()->where('termCode = ?', (string) $app->req->post['regTerm'])->findOne();
                $deleteDate = date('Y-m-d', strtotime($term->dropAddEndDate . ' + 1 days'));

                /* Add courses to the shopping cart. */
                $size = count($app->req->post['courseSecID']);
                $i = 0;
                while ($i < $size) {
                    $q2 = $app->db->stu_rgn_cart();
                    $q2->stuID = get_persondata('personID');
                    $q2->courseSecID = $app->req->post['courseSecID'][$i];
                    $q2->deleteDate = $deleteDate;
                    $q2->save();
                    ++$i;
                }
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'courses/cart' . '/');
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'courses/cart' . '/');
            } catch (Exception $e) {
                Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'courses/cart' . '/');
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'courses/cart' . '/');
            }
        }

        try {
            $terms = _escape(get_option('open_terms'));
            if (function_exists('create_payment_plan') && isStudent(get_persondata('personID'))) {
                $sect = $app->db->course_sec()
                    ->setTableAlias('a')
                    ->select('a.courseSecID,a.courseSecCode,a.secShortTitle,a.dotw')
                    ->select('a.startTime,a.endTime,a.minCredit,a.termCode')
                    ->select('a.courseFee,a.labFee,a.materialFee,a.facID')
                    ->select('a.comment,a.courseSection,b.locationName,c.courseDesc')
                    ->_join('location', 'a.locationCode = b.locationCode', 'b')
                    ->_join('course', 'a.courseID = c.courseID', 'c')
                    ->_join('prog_crse', 'c.courseCode = d.crseCode', 'd')
                    ->_join('stu_program', 'd.progCode = e.acadProgCode', 'e')
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
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        $app->view->display('courses/index', [
            'title' => 'Search Courses',
            'cssArray' => $css,
            'jsArray' => $js,
            'sect' => $q
            ]
        );
    });

    $app->get('/cart/', function () use($app, $css, $js) {
        try {
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
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        $app->view->display('courses/cart', [
            'title' => 'My Shopping Cart',
            'cssArray' => $css,
            'jsArray' => $js,
            'cart' => $q
            ]
        );
    });

    $app->post('/reg/', function () use($app, $css, $js) {
        $uname = get_persondata('uname');

        try {
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
            $counts = array_count_values($app->req->post['regAction']);
            if (bcadd(count($d[0]['id']), $counts['register']) > get_option('number_of_courses')) {
                _etsis_flash()->error(sprintf(_t('Your institution has set a course registration limit. You are only allowed to register for <strong>%s courses</strong> per term.'), get_option('number_of_courses')), $app->req->server['HTTP_REFERER']);
                exit();
            }
            /**
             * If the action selected was 'remove', then remove the record from
             * the student's shopping cart.
             */
            $count = count($app->req->post['regAction']);
            $t = 0;
            while ($t < $count) {
                if ($app->req->post['regAction'][$t] == 'remove') {
                    $app->db->stu_rgn_cart()
                        ->where('stuID = ?', get_persondata('personID'))->_and_()
                        ->where('courseSecID = ?', $app->req->post['courseSecID'][$t])
                        ->delete();
                }
                ++$t;
            }
            /**
             * If the action selected was 'register', then query the database for
             * the required information and add new records to stu_course_sec as
             * well as stu_acad_cred.
             */
            $size = count($app->req->post['regAction']);
            $r = 0;
            while ($r < $size) {
                if ($app->req->post['regAction'][$r] == 'register') {
                    $sect = $app->db->course_sec()
                        ->setTableAlias('a')
                        ->select('a.courseSecID,a.courseSecCode,a.termCode,a.minCredit')
                        ->select('a.courseID,a.sectionNumber,a.courseSection')
                        ->select('a.startDate,a.endDate,b.deptCode')
                        ->select('b.courseShortTitle,b.courseLongTitle')
                        ->select('b.subjectCode,b.creditType,b.courseCode')
                        ->select('b.acadLevelCode,b.courseLevelCode,c.reportingTerm')
                        ->_join('course', 'a.courseID = b.courseID', 'b')
                        ->_join('term', 'a.termCode = c.termCode', 'c')
                        ->where('a.courseSecID = ?', $app->req->post['courseSecID'][$r]);
                    $sect->findOne();

                    $stcs = $app->db->stu_course_sec();
                    $stcs->stuID = get_persondata('personID');
                    $stcs->courseSecID = $app->req->post['courseSecID'][$r];
                    $stcs->courseSecCode = _h($sect->courseSecCode);
                    $stcs->courseSection = _h($sect->courseSection);
                    $stcs->termCode = _h($sect->termCode);
                    $stcs->courseCredits = _h($sect->minCredit);
                    $stcs->status = 'N';
                    $stcs->regDate = $app->db->NOW();
                    $stcs->regTime = date("h:i A");
                    $stcs->statusDate = $app->db->NOW();
                    $stcs->statusTime = date("h:i A");
                    $stcs->addedBy = get_persondata('personID');

                    if ($stcs->save()) {
                        $stac = $app->db->stu_acad_cred();
                        $stac->stuID = get_persondata('personID');
                        $stac->courseID = _h($sect->courseID);
                        $stac->courseSecID = $app->req->post['courseSecID'][$r];
                        $stac->courseCode = _h($sect->courseCode);
                        $stac->courseSecCode = _h($sect->courseSecCode);
                        $stac->sectionNumber = _h($sect->sectionNumber);
                        $stac->courseSection = _h($sect->courseSection);
                        $stac->termCode = _h($sect->termCode);
                        $stac->reportingTerm = _h($sect->reportingTerm);
                        $stac->subjectCode = _h($sect->subjectCode);
                        $stac->deptCode = _h($sect->deptCode);
                        $stac->shortTitle = _h($sect->courseShortTitle);
                        $stac->longTitle = _h($sect->courseLongTitle);
                        $stac->compCred = '0.0';
                        $stac->attCred = _h($sect->minCredit);
                        $stac->status = 'N';
                        $stac->statusDate = $app->db->NOW();
                        $stac->statusTime = date("h:i A");
                        $stac->acadLevelCode = _h($sect->acadLevelCode);
                        $stac->courseLevelCode = _h($sect->courseLevelCode);
                        $stac->creditType = _h($sect->creditType);
                        $stac->startDate = _h($sect->startDate);
                        $stac->endDate = _h($sect->endDate);
                        $stac->addedBy = get_persondata('personID');
                        $stac->addDate = $app->db->NOW();
                        $stac->save();
                        $ID = $stac->lastInsertId();
                        $now = $app->db->NOW();
                        /**
                         * @since 6.1.07
                         */
                        $sacd = $app->db->stu_acad_cred()
                            ->setTableAlias('stac')
                            ->select('stac.*,nae.uname,nae.fname,nae.lname,nae.email')
                            ->_join('person', 'stac.stuID = nae.personID', 'nae')
                            ->where('stac.stuAcadCredID = ?', $ID)
                            ->findOne();
                        /**
                         * Fires after registration and after new STAC record
                         * is added to the database.
                         * 
                         * @since 6.1.05
                         * @param array $sacd Student Academic Credit detail data object.
                         */
                        $app->hook->do_action('post_save_myet_reg', $sacd);
                        /**
                         * Delete the record from the shopping cart after
                         * registration is complete.
                         */
                        $app->db->stu_rgn_cart()
                            ->where('stuID = ?', get_persondata('personID'))->_and_()
                            ->where('courseSecID = ?', $app->req->post['courseSecID'][$r])
                            ->delete();

                        if (function_exists('financial_module')) {
                            /**
                             * Generate bill and/or add fees.
                             */
                            generate_stu_bill(_h($sect->termCode), get_persondata('personID'), $app->req->post['courseSecID'][$r]);
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
            $app->hook->do_action('myet_student_course_registration');

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
                        _etsis_email()->course_registration(get_persondata('personID'), $app->req->post['termCode'], get_base_url());
                    }
                }
                etsis_cache_flush_namespace('student_account');
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } else {
                _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        }
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

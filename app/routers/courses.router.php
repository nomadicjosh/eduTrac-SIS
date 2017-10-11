<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
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
$app->before('GET|POST', '/courses(.*)', function() use($app) {

    if (get_option('enable_myetsis_portal') == 0 && !hasPermission('edit_myetsis_css')) {
        etsis_redirect(get_base_url() . 'offline' . '/');
        exit();
    }

    $app->hook->do_action('execute_webreg_check');
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
                $course_count = $app->db->stcs()
                    ->where('stuID = ?', get_persondata('personID'))->_and_()
                    ->where('termCode = ?', $app->req->post['regTerm'])->_and_()
                    ->whereIn('status', ['A', 'N'])
                    ->count('id');

                if (bcadd($course_count, count($app->req->post['courseSecID'])) > get_option('number_of_courses')) {
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
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'courses/cart' . '/');
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'courses/cart' . '/');
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'courses/cart' . '/');
            }
        }

        try {
            $terms = _escape(get_option('open_terms'));
            if (function_exists('create_payment_plan') && is_student(get_persondata('personID'))) {
                $sect = $app->db->course_sec()
                    ->setTableAlias('a')
                    ->select('a.courseSecID,a.courseSecCode,a.secShortTitle,a.dotw')
                    ->select('a.startTime,a.endTime,a.minCredit,a.termCode')
                    ->select('a.courseFee,a.labFee,a.materialFee,a.facID')
                    ->select('a.comment,a.courseSection,b.locationName,c.courseDesc,c.courseID')
                    ->_join('location', 'a.locationCode = b.locationCode', 'b')
                    ->_join('course', 'a.courseID = c.courseID', 'c')
                    ->_join('prog_crse', 'c.courseCode = d.crseCode', 'd')
                    ->_join('sacp', 'd.progCode = sacp.acadProgCode')
                    ->where('sacp.stuID = ?', get_persondata('personID'))->_and_()
                    ->where('a.currStatus = "A"')->_and_()
                    ->where('a.webReg = "1"')->_and_()
                    ->where('a.termCode IN(' . $terms . ')');
            } else {
                $sect = $app->db->course_sec()
                    ->setTableAlias('a')
                    ->select('a.courseSecID,a.courseSecCode,a.secShortTitle,a.dotw')
                    ->select('a.startTime,a.endTime,a.minCredit,a.termCode')
                    ->select('a.courseFee,a.labFee,a.materialFee,a.facID')
                    ->select('a.comment,a.courseSection,b.locationName,c.courseDesc,c.courseID')
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
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
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
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
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

    /**
     * Before router check.
     */
    $app->before('POST', '/reg', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        }

        if (!checkStuAccess(get_persondata('personID'))) {
            _etsis_flash()->error(_t('Only active students can register for courses.'), get_base_url());
        }

        if (get_option('enable_myetsis_portal') == 0 && !hasPermission('edit_myetsis_css')) {
            etsis_redirect(get_base_url() . 'offline' . '/');
        }
    });

    $app->post('/reg/', function () use($app, $css, $js) {
        try {
            /**
             * Checks to see how many courses the student has already registered 
             * for the requested semester. If the student has already registered for 
             * courses, then count them and add it to the number they are trying to 
             * register for. If that number is greater than the number_of_courses 
             * restriction, then redirect the student to a error page, otherwise 
             * let the registration go through.
             */
            $course_count = $app->db->stcs()
                ->where('stuID = ?', get_persondata('personID'))->_and_()
                ->where('termCode = ?', get_option('registration_term'))->_and_()
                ->whereIn('status', ['A', 'N'])
                ->count('id');

            $counts = array_count_values($app->req->post['regAction']);
            if (bcadd($course_count, $counts['register']) > get_option('number_of_courses')) {
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
             * the required information and add new records to stcs as
             * well as stac.
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
                        ->where('a.courseSecID = ?', $app->req->post['courseSecID'][$r])
                        ->findOne();

                    $stcs = $app->db->stcs();
                    $stcs->stuID = get_persondata('personID');
                    $stcs->courseSecID = $app->req->post['courseSecID'][$r];
                    $stcs->courseSecCode = _escape($sect->courseSecCode);
                    $stcs->courseSection = _escape($sect->courseSection);
                    $stcs->termCode = _escape($sect->termCode);
                    $stcs->courseCredits = _escape($sect->minCredit);
                    $stcs->status = 'N';
                    $stcs->regDate = Jenssegers\Date\Date::now();
                    $stcs->regTime = Jenssegers\Date\Date::now()->format('h:i A');
                    $stcs->statusDate = Jenssegers\Date\Date::now();
                    $stcs->statusTime = Jenssegers\Date\Date::now()->format('h:i A');
                    $stcs->addedBy = get_persondata('personID');
                    $stcs->save();

                    $stac = $app->db->stac();
                    $stac->stuID = get_persondata('personID');
                    $stac->courseID = _escape($sect->courseID);
                    $stac->courseSecID = $app->req->post['courseSecID'][$r];
                    $stac->courseCode = _escape($sect->courseCode);
                    $stac->courseSecCode = _escape($sect->courseSecCode);
                    $stac->sectionNumber = _escape($sect->sectionNumber);
                    $stac->courseSection = _escape($sect->courseSection);
                    $stac->termCode = _escape($sect->termCode);
                    $stac->reportingTerm = _escape($sect->reportingTerm);
                    $stac->subjectCode = _escape($sect->subjectCode);
                    $stac->deptCode = _escape($sect->deptCode);
                    $stac->shortTitle = _escape($sect->courseShortTitle);
                    $stac->longTitle = _escape($sect->courseLongTitle);
                    $stac->compCred = '0.0';
                    $stac->attCred = _escape($sect->minCredit);
                    $stac->status = 'N';
                    $stac->statusDate = Jenssegers\Date\Date::now();
                    $stac->statusTime = Jenssegers\Date\Date::now()->format('h:i A');
                    $stac->acadLevelCode = _escape($sect->acadLevelCode);
                    $stac->courseLevelCode = _escape($sect->courseLevelCode);
                    $stac->creditType = _escape($sect->creditType);
                    $stac->startDate = _escape($sect->startDate);
                    $stac->endDate = if_null(_escape($sect->endDate));
                    $stac->addedBy = get_persondata('personID');
                    $stac->addDate = Jenssegers\Date\Date::now();
                    $stac->save();
                    $_id = $stac->lastInsertId();
                    /**
                     * @since 6.1.07
                     */
                    $sacd = $app->db->stac()
                        ->select('stac.*,nae.uname,nae.fname,nae.lname,nae.email')
                        ->_join('person', 'stac.stuID = nae.personID', 'nae')
                        ->where('stac.id = ?', $_id)
                        ->findOne();
                    /**
                     * Fires after registration and after new STAC record
                     * is added to the database.
                     * 
                     * @since 6.1.05
                     * @param array $sacd Student Academic Credit detail data object.
                     */
                    $app->hook->do_action('post_save_myetsis_reg', $sacd);
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
                        generate_stu_bill(_escape($sect->termCode), get_persondata('personID'), $app->req->post['courseSecID'][$r]);
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
            $app->hook->do_action('myetsis_student_course_registration');

            /**
             * Add registerd courses to registration node for later processing.
             * 
             * @since 6.3.0
             */
            $st = $app->db->stac()
                ->select('courseSection')
                ->where('stuID = ?', get_persondata('personID'))->_and_()
                ->where('LastUpdate BETWEEN ? AND ?', [Jenssegers\Date\Date::now()->sub('2 minutes'), Jenssegers\Date\Date::now()]);
            $st->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = _escape($d['courseSection']);
                }
                if ($array != null) {
                    try {
                        Node::dispense('crse_rgn');
                        $sect = Node::table('crse_rgn');
                        $sect->stuid = (int) get_persondata('personID');
                        $sect->sections = implode(',', $array);
                        $sect->timestamp = Jenssegers\Date\Date::now()->format('D, M d, o @ h:i A');
                        $sect->sent = 0;
                        $sect->save();
                    } catch (NodeQException $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    } catch (Exception $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    }
                }
            });
            etsis_cache_flush_namespace('student_account');
            _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        }
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

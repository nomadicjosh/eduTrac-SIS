<?php

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

/**
 * Student Router
 *
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$css = ['css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
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

$app->group('/stu', function() use ($app, $css, $js) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }

        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $post = $app->req->post['spro'];

                $spro = $app->db->student()
                        ->setTableAlias('a')
                        ->select('a.stuID,b.lname,b.fname,b.email')
                        ->_join('person', 'a.stuID = b.personID', 'b')
                        ->whereLike('CONCAT(b.fname," ",b.lname)', "%$post%")->_or_()
                        ->whereLike('CONCAT(b.lname," ",b.fname)', "%$post%")->_or_()
                        ->whereLike('CONCAT(b.lname,", ",b.fname)', "%$post%")->_or_()
                        ->whereLike('b.altID', "%$post%")->_or_()
                        ->whereLike('b.uname', "%$post%")->_or_()
                        ->whereLike('a.stuID', "%$post%");

                $q = $spro->find(function($data) {
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

        $app->view->display('student/index', [
            'title' => 'Student Lookup',
            'search' => $q
                ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }

        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js) {
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
            $spro->update();

            etsis_cache_delete($id, 'stu');
            /**
             * Triggers after SPRO record is updated.
             * 
             * @since 6.1.05
             * @param object $spro Student profile data object.
             * @return mixed
             */
            $app->hook->do_action('post_update_spro', $spro);
            etsis_logger_activity_log_write('Update Record', 'Student Profile (SPRO)', get_name($id), get_persondata('uname'));
            _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
        }

        try {
            $spro = $app->db->student()
                    ->where('stuID', $id)
                    ->findOne();

            $admit = $app->db->application()
                    ->where('personID = ?', (int) $id)
                    ->findOne();

            $prog = $app->db->query("SELECT 
                    sacp.id,sacp.stuID,sacp.acadProgCode,sacp.currStatus,
                    sacp.statusDate,sacp.startDate,sacp.approvedBy,b.acadLevelCode AS progAcadLevel,
                    b.locationCode,
                    CASE c.status 
                    WHEN 'A' Then 'Active' 
                    ELSE 'Inactive' 
                    END AS 'stuStatus',c.tags 
                FROM sacp 
                LEFT JOIN acad_program b ON sacp.acadProgCode = b.acadProgCode 
                LEFT JOIN student c ON sacp.stuID = c.stuID 
                WHERE sacp.stuID = ? 
                ORDER BY sacp.statusDate", [$id]
            );

            $q = $prog->find(function($data) {
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

            etsis_register_style('form');
            etsis_register_style('selectize');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('student/view', [
                'title' => get_name($id),
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
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }

        if (!hasPermission('create_stu_record')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $nae = get_person_by('personID', $id);
                if (_escape($nae->ssn) > 0) {
                    $pass = str_replace('-', '', _escape($nae->ssn));
                } elseif (_escape($nae->dob) != '0000-00-00') {
                    $pass = str_replace('-', '', _escape($nae->dob));
                } else {
                    $pass = 'myaccount';
                }
                $degree = $app->db->acad_program()->where('acadProgCode = ?', _trim($app->req->post['acadProgCode']))->findOne();
                $appl = $app->db->application()->where('personID = ?', $id)->findOne();

                $student = $app->db->student();
                $student->stuID = $id;
                $student->status = $app->req->post['status'];
                $student->tags = $app->req->post['tags'];
                $student->addDate = \Jenssegers\Date\Date::now();
                $student->approvedBy = get_persondata('personID');
                $student->save();

                $sacp = $app->db->sacp();
                $sacp->stuID = $id;
                $sacp->acadProgCode = _trim($app->req->post['acadProgCode']);
                $sacp->currStatus = 'A';
                $sacp->statusDate = \Jenssegers\Date\Date::now();
                $sacp->startDate = $app->req->post['startDate'];
                $sacp->approvedBy = get_persondata('personID');
                $sacp->antGradDate = $app->req->post['antGradDate'];
                $sacp->advisorID = $app->req->post['advisorID'];
                $sacp->catYearCode = _trim($app->req->post['catYearCode']);
                $sacp->save();

                $al = $app->db->stal();
                $al->stuID = $id;
                $al->acadProgCode = _trim($app->req->post['acadProgCode']);
                $al->acadLevelCode = _trim($app->req->post['acadLevelCode']);
                $al->save();

                /**
                 * Fires before new student record is created.
                 *
                 * @since 6.1.07
                 * @param int $id Student's ID.
                 */
                $app->hook->do_action('pre_save_stu', $id);

                if (_escape(get_option('send_acceptance_email')) == 1) {
                    $host = get_domain_name();
                    $site = _t('myetSIS :: ') . _escape(get_option('institution_name'));
                    $message = _escape(get_option('student_acceptance_letter'));
                    $message = str_replace('#uname#', _escape($nae->uname), $message);
                    $message = str_replace('#fname#', _escape($nae->fname), $message);
                    $message = str_replace('#lname#', _escape($nae->lname), $message);
                    $message = str_replace('#name#', get_name($id), $message);
                    $message = str_replace('#id#', $id, $message);
                    $message = str_replace('#email#', _escape($nae->email), $message);
                    $message = str_replace('#sacp#', _trim($app->req->post['acadProgCode']), $message);
                    $message = str_replace('#acadlevel#', _trim($app->req->post['acadLevelCode']), $message);
                    $message = str_replace('#degree#', _trim(_escape($degree->degreeCode)), $message);
                    $message = str_replace('#startterm#', _escape($appl->startTerm), $message);
                    $message = str_replace('#adminemail#', _escape(get_option('system_email')), $message);
                    $message = str_replace('#url#', get_base_url(), $message);
                    $message = str_replace('#helpdesk#', _escape(get_option('help_desk')), $message);
                    $message = str_replace('#currentterm#', _escape(get_option('current_term_code')), $message);
                    $message = str_replace('#instname#', _escape(get_option('institution_name')), $message);
                    $message = str_replace('#mailaddr#', _escape(get_option('mailing_address')), $message);
                    $message = process_email_html($message, _t("Student Acceptance Letter"));

                    $headers[] = sprintf("From: %s <auto-reply@%s>", $site, $host);
                    try {
                        _etsis_email()->etsisMail(_escape($nae->email), _t("Student Acceptance Letter"), $message, $headers);
                    } catch (phpmailerException $e) {
                        Cascade::getLogger('error')->error($e->getMessage());
                        _etsis_flash()->error($e->getMessage());
                    } catch (Exception $e) {
                        Cascade::getLogger('error')->error($e->getMessage());
                        _etsis_flash()->error($e->getMessage());
                    }
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

                etsis_logger_activity_log_write('New Record', 'Student', get_name($id), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'stu/' . $id . '/');
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
            $stu = $app->db->acad_program()
                    ->setTableAlias('a')
                    ->select('a.id,a.acadProgCode,a.acadProgTitle')
                    ->select('a.acadLevelCode,b.majorName,c.locationName')
                    ->select('d.schoolName,e.personID,e.startTerm,aclv.comp_months')
                    ->_join('major', 'a.majorCode = b.majorCode', 'b')
                    ->_join('location', 'a.locationCode = c.locationCode', 'c')
                    ->_join('school', 'a.schoolCode = d.schoolCode', 'd')
                    ->_join('application', 'a.acadProgCode = e.acadProgCode', 'e')
                    ->_join('student', 'e.personID = f.stuID', 'f')
                    ->_join('aclv', 'a.acadLevelCode = aclv.code')
                    ->where('e.personID = ?', $id)->_and_()
                    ->whereNull('f.stuID');

            $q = $stu->find(function($data) {
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
        if ($q === false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, redirect to the current
         * student record.
         */ elseif (count($q) <= 0) {

            etsis_redirect(get_base_url() . 'stu' . '/' . $id . '/');
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('selectize');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datepicker');

            $app->view->display('student/add', [
                'title' => 'Create Student Record',
                'student' => $q
                    ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/stac/(\d+)/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        }

        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/stac/(\d+)/', function ($id) use($app) {

        try {
            $stac = $app->db->stac()
                    ->select('id,stuID,attCred,ceu')
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

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datatables');

            $app->view->display('student/stac', [
                'title' => get_name($id),
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
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        }

        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/sttr/(\d+)/', function ($id) use($app) {

        try {
            $sttr = $app->db->sttr()
                    ->select('sttr.termCode,sttr.acadLevelCode,sttr.attCred,sttr.compCred')
                    ->select('sttr.stuID,sttr.gradePoints,sttr.gpa,sttr.stuLoad')
                    ->select('b.termStartDate,b.termEndDate')
                    ->_join('term', 'sttr.termCode = b.termCode', 'b')
                    ->_join('stcs', 'sttr.termCode = c.termCode AND sttr.stuID = c.stuID', 'c')
                    ->where('sttr.stuID = ?', $id)
                    ->groupBy('sttr.termCode, sttr.stuID')
                    ->orderBy('sttr.termCode', 'ASC');

            $q = $sttr->find(function($data) {
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

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datatables');

            $app->view->display('student/sttr', [
                'title' => get_name($id),
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
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }

        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/shis/(\d+)/', function ($id) use($app) {

        if ($app->req->isPost()) {
            try {
                if (isset($app->req->post['id'])) {
                    $size = count($app->req->post['id']);
                    $i = 0;
                    while ($i < $size) {
                        $shis = $app->db->hiatus();
                        $shis->set([
                                    'code' => $app->req->post['code'][$i],
                                    'startDate' => $app->req->post['startDate'][$i],
                                    'endDate' => if_null($app->req->post['endDate'][$i]),
                                    'comment' => $app->req->post['comment'][$i]
                                ])
                                ->where('stuID = ?', $id)->_and_()
                                ->where('id = ?', $app->req->post['id'][$i])
                                ->update();
                        ++$i;
                    }
                    etsis_logger_activity_log_write('Update Record', 'Student Hiatus', get_name($id), get_persondata('uname'));
                } else {
                    $shis = $app->db->hiatus();
                    $shis->insert([
                        'stuID' => $app->req->post['stuID'],
                        'code' => $app->req->post['code'],
                        'startDate' => $app->req->post['startDate'],
                        'endDate' => if_null($app->req->post['endDate']),
                        'comment' => if_null($app->req->post['comment']),
                        'addDate' => $app->req->post['addDate'],
                        'addedBy' => $app->req->post['addedBy']
                    ]);
                    etsis_logger_activity_log_write('New Record', 'Student Hiatus (SHIS)', get_name($id), get_persondata('uname'));
                }
                etsis_cache_delete($id, 'stu');
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
            $spro = get_student($id);

            $shis = $app->db->query("SELECT 
                    CASE code 
                	WHEN 'W' THEN 'Withdrawal'
                	WHEN 'LOA' THEN 'Leave of Absence'
                	WHEN 'SA' THEN 'Study Abroad'
                	WHEN 'ILL' THEN 'Illness'
                	ELSE 'Dismissed'
                	END AS 'Code',
                	id,stuID,code,startDate,endDate,comment 
                    FROM hiatus 
                    WHERE stuID = ? 
                    ORDER BY id DESC", [_escape($spro->stuID)]
            );

            $q = $shis->find(function($data) {
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
         */ elseif (_escape($spro->stuID) <= 0) {

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

            $app->view->display('student/shis', [
                'title' => get_name($id),
                'shis' => $q,
                'stu' => $spro
                    ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/sacd/(\d+)/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }

        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/sacd/(\d+)/', function ($id) use($app) {

        $stac = $app->db->stac()
                ->where('id = ?', (int) $id)
                ->findOne();

        $date = \Jenssegers\Date\Date::now()->format("Y-m-d");
        $time = \Jenssegers\Date\Date::now()->format("h:m A");

        if ($app->req->isPost()) {
            try {
                $term = $app->db->term()
                        ->where('termCode = ?', $app->req->post['termCode'])
                        ->findOne();

                $detail = $app->db->stac();
                $detail->courseID = $app->req->post['courseID'];
                $detail->courseSecID = _escape($stac->courseSecID) != '' ? _escape($stac->courseSecID) : NULL;
                $detail->courseCode = $app->req->post['courseCode'];
                $detail->courseSecCode = _escape($stac->courseSecCode) != '' ? _escape($stac->courseSecCode) : NULL;
                $detail->sectionNumber = $app->req->post['sectionNumber'] != '' ? $app->req->post['sectionNumber'] : NULL;
                $detail->courseSection = _escape($stac->courseSection) != '' ? _escape($stac->courseSection) : NULL;
                $detail->termCode = $app->req->post['termCode'];
                $detail->reportingTerm = _escape($term->reportingTerm);
                $detail->subjectCode = $app->req->post['subjectCode'];
                $detail->deptCode = $app->req->post['deptCode'];
                $detail->shortTitle = $app->req->post['shortTitle'];
                $detail->longTitle = $app->req->post['longTitle'];
                $detail->attCred = $app->req->post['attCred'];
                $detail->ceu = $app->req->post['ceu'];
                $detail->status = $app->req->post['status'];
                $detail->acadLevelCode = $app->req->post['acadLevelCode'];
                $detail->courseLevelCode = $app->req->post['courseLevelCode'];
                $detail->creditType = $app->req->post['creditType'];
                $detail->startDate = $app->req->post['startDate'];
                $detail->endDate = if_null($app->req->post['endDate']);
                if (($app->req->post['status'] == 'W' || $app->req->post['status'] == 'D') && $date >= _escape($term->termStartDate) && $date > _escape($term->dropAddEndDate)) {
                    $detail->compCred = '0.0';
                    $detail->gradePoints = calculate_grade_points($app->req->post['grade'], '0.0');
                    $detail->statusTime = $time;
                    if (empty($app->req->post['grade'])) {
                        $detail->grade = "W";
                    } else {
                        $detail->grade = $app->req->post['grade'];
                    }
                } else {
                    if (calculate_grade_points($app->req->post['grade'], $app->req->post['attCred']) > 0) {
                        $compCred = $app->req->post['attCred'];
                    } else {
                        $compCred = '0';
                    }
                    $detail->compCred = $compCred;
                    $detail->gradePoints = calculate_grade_points($app->req->post['grade'], $app->req->post['attCred']);
                    $detail->grade = if_null($app->req->post['grade']);
                }
                $detail->where('id = ?', $id);

                /**
                 * If the posted status is 'W' or 'D' and today's date is less than the 
                 * primary term start date, then delete all student course sec as well as 
                 * student acad cred records.
                 */
                if (($app->req->post['status'] == 'W' || $app->req->post['status'] == 'D') && $date < _escape($term->termStartDate)) {
                    $q = $app->db->stcs()
                            ->where('stuID = ?', _escape((int) $stac->stuID))->_and_()
                            ->where('courseSection = ?', _escape($stac->courseSection))
                            ->delete();
                    $q = $app->db->stac()->where('id = ?', $id)->delete();

                    if (function_exists('financial_module')) {
                        $q = $app->db->stu_acct_fee()->where('stuID = ?', $stac->stuID)->_and_()->where('description = ?', _escape($stac->courseSection))->delete();
                        /**
                         * Begin Updating tuition totals.
                         */
                        $total = qt('course_sec', 'courseFee', 'courseSection = "' . _escape($stac->courseSection) . '"') + qt('course_sec', 'labFee', 'courseSection = "' . _escape($stac->courseSection) . '"') + qt('course_sec', 'materialFee', 'courseSection = "' . _escape($stac->courseSection) . '"');
                        $stuTuition = $app->db->stu_acct_tuition()->where('stuID = ? AND termCode = ?', [_escape($stac->stuID), $app->req->post['termCode']])->findOne();
                        $q = $app->db->stu_acct_tuition();
                        $q->total = bcsub($stuTuition->total, $total);
                        $q->where('stuID = ?', _escape((int) $stac->stuID))->_and_()->where('termCode = ?', $app->req->post['termCode'])->update();
                        /**
                         * End updating tuition totals.
                         */
                    }

                    etsis_redirect(get_base_url() . 'stu/stac' . '/' . _escape((int) $stac->stuID) . '/');
                    exit();
                }
                /**
                 * If posted status is 'W' or 'D' and today's date is greater than equal to the 
                 * primary term start date, and today's date is less than the term's drop/add 
                 * end date, then delete all student course sec as well as student acad cred 
                 * records.
                 */ elseif (($app->req->post['status'] == 'W' || $app->req->post['status'] == 'D') && $date >= _escape($term->termStartDate) && $date < _escape($term->dropAddEndDate)) {
                    $q = $app->db->stcs()
                            ->where('stuID = ?', _escape((int) $stac->stuID))->_and_()
                            ->where('courseSection = ?', _escape($stac->courseSection))
                            ->delete();
                    $q = $app->db->stac()->where('id = ?', $id)->delete();

                    if (function_exists('financial_module')) {
                        $q = $app->db->stu_acct_fee()->where('stuID = ?', _escape((int) $stac->stuID))->_and_()->where('description = ?', _escape($stac->courseSection))->delete();
                        /**
                         * Begin Updating tuition totals.
                         */
                        $total = qt('course_sec', 'courseFee', 'courseSection = "' . _escape($stac->courseSection) . '"') + qt('course_sec', 'labFee', 'courseSection = "' . _escape($stac->courseSection) . '"') + qt('course_sec', 'materialFee', 'courseSection = "' . _escape($stac->courseSection) . '"');
                        $q = $app->db->stu_acct_tuition();
                        $q->total = bcsub($q->total, $total);
                        $q->where('stuID = ?', _escape((int) $stac->stuID))->_and_()->where('termCode = ?', $app->req->post['termCode'])->update();
                        /**
                         * End updating tuition totals.
                         */
                    }

                    etsis_redirect(get_base_url() . 'stu/stac' . '/' . _escape((int) $stac->stuID) . '/');
                    exit();
                }
                /**
                 * If posted status is 'W' or 'D' and today's date is greater than equal to the 
                 * primary term start date, and today's date is greater than the term's drop/add 
                 * end date, then update student course sec record with a 'W' status and update  
                 * student acad record with a 'W' grade and 0.0 completed credits.
                 */ elseif (($app->req->post['status'] == 'W' || $app->req->post['status'] == 'D') && $date >= _escape($term->termStartDate) && $date > _escape($term->dropAddEndDate)) {
                    $q = $app->db->stcs();
                    $q->courseSecCode = $app->req->post['courseSecCode'];
                    $q->termCode = $app->req->post['termCode'];
                    $q->courseCredits = $app->req->post['attCred'];
                    $q->status = $app->req->post['status'];
                    $q->statusDate = \Jenssegers\Date\Date::now();
                    $q->statusTime = $time;
                    $q->where('stuID = ?', _escape((int) $stac->stuID))->_and_()
                            ->where('id = ?', $app->req->post['courseSecID'])
                            ->update();
                    $detail->update();
                }
                /**
                 * If there is no status change or the status change is not a 'W', 
                 * just update stcs and stac records with the 
                 * changed information.
                 */ else {
                    $q = $app->db->stcs();
                    $q->courseSecCode = $app->req->post['courseSecCode'];
                    $q->termCode = $app->req->post['termCode'];
                    $q->courseCredits = $app->req->post['attCred'];
                    $q->status = $app->req->post['status'];
                    $q->statusDate = $app->req->post['statusDate'];
                    $q->statusTime = $app->req->post['statusTime'];
                    $q->where('stuID = ?', _escape((int) $stac->stuID))->_and_()
                            ->where('id = ?', $app->req->post['courseSecID'])
                            ->update();
                    $detail->update();
                }
                /**
                 * @since 6.1.08
                 */
                $sacd = $app->db->stac()
                        ->select('stac.*,nae.uname,nae.fname,nae.lname,nae.email')
                        ->_join('person', 'stac.stuID = nae.personID', 'nae')
                        ->where('stac.id = ?', $id)
                        ->findOne();
                /**
                 * Triggers after SACD record is updated.
                 * 
                 * @since 6.1.05
                 * @param array $sacd Student Academic Credit Detail data object.
                 */
                $app->hook->do_action('post_update_sacd', $sacd);
                etsis_cache_delete($id, 'stu');
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
        if ($stac == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($stac) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($stac) <= 0) {

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
            etsis_register_script('timepicker');

            $app->view->display('student/sacd', [
                'title' => get_name(_escape((int) $stac->stuID)),
                'sacd' => $stac
                    ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/sacp/(\d+)/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }

        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/sacp/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $sacp = $app->db->sacp();
                foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                    $sacp->$k = $v;
                }
                $sacp->where('id = ?', $id);
                $sacp->update();

                if ($app->req->post['currStatus'] == 'C' || $app->req->post['currStatus'] == 'W') {
                    $_sacp = $app->db->sacp()
                            ->select('acadProgCode')
                            ->where('stuID = ?', $app->req->post['stuID'])->_and_()
                            ->where('id = ?', $id)
                            ->findOne();

                    $stal = $app->db->stal()
                            ->where('stuID = ?', $app->req->post['stuID'])->_and_()
                            ->where('acadProgCode = ?', _escape($_sacp->acadProgCode))
                            ->findOne();
                    $stal->set([
                                'endDate' => if_null($app->req->post['endDate'])
                            ])
                            ->update();
                }

                etsis_logger_activity_log_write('Update Record', 'Student Acad Program (SACP)', get_name($app->req->post['stuID']), get_persondata('uname'));
                etsis_cache_delete($id, 'stu');
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
            $sacp = $app->db->acad_program()
                    ->setTableAlias('a')
                    ->select('a.acadProgCode,a.schoolCode,a.acadLevelCode,sacp.id')
                    ->select('sacp.eligible_to_graduate,sacp.graduationDate,sacp.antGradDate')
                    ->select('sacp.stuID,sacp.advisorID,sacp.catYearCode,sacp.currStatus')
                    ->select('sacp.statusDate,sacp.startDate,sacp.endDate,sacp.comments')
                    ->select('sacp.approvedBy,sacp.LastUpdate,c.schoolName')
                    ->_join('sacp', 'a.acadProgCode = sacp.acadProgCode')
                    ->_join('school', 'a.schoolCode = c.schoolCode', 'c')
                    ->where('sacp.id = ?', $id);
            $q = $sacp->find(function($data) {
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

            etsis_register_style('form');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datepicker');

            $app->view->display('student/sacp', [
                'title' => get_name(_escape($q[0]['stuID'])),
                'sacp' => $q
                    ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/sacp/(\d+)/stal/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }

        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/sacp/(\d+)/stal/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $stal = $app->db->stal()
                        ->where('id = ?', $app->req->post['stalID'])
                        ->findOne();
                $stal->set([
                            'acadLevelCode' => $app->req->post['acadLevelCode'] == 'NULL' ? NULL : $app->req->post['acadLevelCode'],
                            'currentClassLevel' => $app->req->post['currentClassLevel'] == 'NULL' ? NULL : $app->req->post['currentClassLevel'],
                            'enrollmentStatus' => $app->req->post['enrollmentStatus'] == 'NULL' ? NULL : $app->req->post['enrollmentStatus'],
                            'gpa' => $app->req->post['gpa'] <= 0 ? 0 : $app->req->post['gpa'],
                            'startTerm' => $app->req->post['startTerm'] == 'NULL' ? NULL : $app->req->post['startTerm'],
                            'startDate' => $app->req->post['startDate'] <= '0000-00-00' || $app->req->post['startDate'] == '' ? NULL : $app->req->post['startDate'],
                            'endDate' => if_null($app->req->post['endDate'])
                        ])
                        ->update();

                etsis_logger_activity_log_write('Update Record', 'Student Academic Level (STAL)', get_name($app->req->post['stuID']), get_persondata('uname'));
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
            $stal = $app->db->sacp()
                    ->select('sacp.id AS sacpID,sacp.stuID,stal.id AS stalID,stal.acadProgCode')
                    ->select('stal.acadLevelCode,stal.currentClassLevel,stal.enrollmentStatus')
                    ->select('stal.gpa,stal.startTerm,stal.startDate,stal.endDate,acad_program.acadProgTitle')
                    ->_join('stal', 'sacp.stuID = stal.stuID AND sacp.acadProgCode = stal.acadProgCode')
                    ->_join('acad_program', 'sacp.acadProgCode = acad_program.acadProgCode')
                    ->where('sacp.id = ?', $id)
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
        if ($stal === false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($stal) === true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_escape($stal->sacpID) <= 0) {

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

            $app->view->display('student/stal', [
                'title' => get_name(_escape($stal->stuID)),
                'stal' => $stal
                    ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/add-prog/(\d+)/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }

        if (!hasPermission('create_stu_record')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/add-prog/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $prog = $app->db->acad_program()
                        ->where('acadProgCode = ?', $app->req->post['acadProgCode'])
                        ->findOne();

                $stal = $app->db->stal()
                        ->where('stuID = ?', $id)->_and_()
                        ->where('acadProgCode = ?', $app->req->post['acadProgCode'])
                        ->count('id');

                $sacp = $app->db->sacp();
                $sacp->insert([
                    'stuID' => $id,
                    'acadProgCode' => _trim($app->req->post['acadProgCode']),
                    'currStatus' => $app->req->post['currStatus'],
                    'statusDate' => \Jenssegers\Date\Date::now(),
                    'startDate' => $app->req->post['startDate'],
                    'endDate' => if_null($app->req->post['endDate']),
                    'approvedBy' => get_persondata('personID'),
                    'antGradDate' => $app->req->post['antGradDate'],
                    'advisorID' => $app->req->post['advisorID'],
                    'catYearCode' => $app->req->post['catYearCode']
                ]);

                if ($stal <= 0) {
                    $al = $app->db->stal();
                    $al->insert([
                        'stuID' => $id,
                        'acadProgCode' => _trim($app->req->post['acadProgCode']),
                        'acadLevelCode' => _escape($prog->acadLevelCode)
                    ]);
                }

                etsis_logger_activity_log_write('New Record', 'Student Academic Program', get_name($id), get_persondata('uname'));
                etsis_cache_delete($id, 'stu');
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'stu' . '/' . $id . '/');
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
            $spro = get_student($id);
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
        if ($spro === false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($spro) === true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_escape($spro->stuID) <= 0) {

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

            $app->view->display('student/add-prog', [
                'title' => get_name($id),
                'stu' => $spro
                    ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/graduation/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }

        if (!hasPermission('graduate_students')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/graduation/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                if (!empty($app->req->post['studentID'])) {
                    $grad = $app->db->sacp()
                            ->where('stuID = ?', $app->req->post['studentID'])->_and_()
                            ->where('eligible_to_graduate = "1"')->_and_()
                            ->where('currStatus = "A"')
                            ->findOne();
                    $grad->set([
                                'statusDate' => \Jenssegers\Date\Date::now(),
                                'endDate' => \Jenssegers\Date\Date::now(),
                                'currStatus' => 'G',
                                'graduationDate' => $app->req->post['gradDate']
                            ])
                            ->update();
                    $stal = $app->db->stal()
                            ->where('stuID = ?', $app->req->post['studentID'])->_and_()
                            ->where('acadProgCode = ?', _escape($grad->acadProgCode))
                            ->findOne();
                    $stal->set([
                                'currentClassLevel' => NULL,
                                'enrollmentStatus' => 'G',
                                'acadStanding' => NULL,
                                'endDate' => $app->req->post['gradDate']
                            ])
                            ->update();
                } else {
                    $grad = $app->db->graduation_hold();
                    $grad->insert([
                        'queryID' => $app->req->post['id'],
                        'gradDate' => $app->req->post['gradDate']
                    ]);
                }
                etsis_logger_activity_log_write('Update Record', 'Graduation', get_name($app->req->post['stuID']), get_persondata('uname'));
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

        etsis_register_style('form');
        etsis_register_style('jquery-ui');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('jquery-ui');
        etsis_register_script('datepicker');

        $app->view->display('student/graduation', [
            'title' => 'Graduation'
                ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/tran.*', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }

        if (!hasPermission('generate_transcripts')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/tran/', function () use($app) {
        if ($app->req->isPost()) {
            etsis_redirect(get_base_url() . 'stu/tran' . '/' . $app->req->post['stuID'] . '/' . $app->req->post['acadLevelCode'] . '/' . $app->req->post['template'] . '/');
        }

        etsis_register_style('form');
        etsis_register_style('jquery-ui');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('jquery-ui');

        $app->view->display('student/tran', [
            'title' => 'Transcript'
                ]
        );
    });

    $app->get('/tran/(\d+)/(\w+)/(\w+)/', function ($id, $level, $template) use($app, $css, $js) {

        try {
            $tranInfo = $app->db->stac()
                    ->select('CASE stac.acadLevelCode WHEN "UG" THEN "Undergraduate" WHEN "GR" THEN "Graduate" '
                            . 'WHEN "Phd" THEN "Doctorate" WHEN "CE" THEN "Continuing Education" WHEN "CTF" THEN "Certificate" '
                            . 'WHEN "DIP" THEN "Diploma" WHEN "PR" THEN "Professional" ELSE "Non-Degree" END AS "Level"')
                    ->select('stac.stuID,b.address1,b.address2,b.city,b.state')
                    ->select('b.zip,c.altID,c.ssn,c.dob,sacp.graduationDate,f.degreeCode')
                    ->select('f.degreeName,g.majorCode,g.majorName,h.minorCode')
                    ->select('h.minorName,i.specCode,i.specName,j.ccdCode,j.ccdName')
                    ->_join('address', 'stac.stuID = b.personID', 'b')
                    ->_join('person', 'stac.stuID = c.personID', 'c')
                    ->_join('sacp', 'stac.stuID = sacp.stuID')
                    ->_join('acad_program', 'sacp.acadProgCode = e.acadProgCode', 'e')
                    ->_join('degree', 'e.degreeCode = f.degreeCode', 'f')
                    ->_join('major', 'e.majorCode = g.majorCode', 'g')
                    ->_join('minor', 'e.minorCode = h.minorCode', 'h')
                    ->_join('specialization', 'e.specCode = i.specCode', 'i')
                    ->_join('ccd', 'e.ccdCode = j.ccdCode', 'j')
                    ->where('stac.stuID = ?', $id)->_and_()
                    ->where('stac.acadLevelCode = ?', $level)->_and_()
                    ->where('b.addressStatus = "C"')->_and_()
                    ->where('b.addressType = "P"')->_and_()
                    ->where('e.acadLevelCode = ?', $level)->_and_()
                    ->where('(sacp.currStatus = "A" OR sacp.currStatus = "G")');
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
                    FROM stac
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

            $tranGPA = $app->db->stac()
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
                    FROM stac  
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

            $transferGPA = $app->db->stac()
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
        if ($info == false) {

            _etsis_flash()->error(_etsis_flash()->notice(204), get_base_url() . 'stu/tran' . '/');
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($info) == true) {

            _etsis_flash()->error(_etsis_flash()->notice(204), get_base_url() . 'stu/tran' . '/');
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($info) <= 0) {

            _etsis_flash()->error(_etsis_flash()->notice(204), get_base_url() . 'stu/tran' . '/');
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/templates/transcript/' . $template . '.template', [
                'title' => 'Print Transcript',
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
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        }

        if (!checkStuAccess(get_persondata('personID'))) {
            etsis_redirect(get_base_url() . 'profile' . '/');
        }

        if (_escape(get_option('enable_myetsis_portal')) == 0 && !hasPermission('edit_myetsis_css')) {
            etsis_redirect(get_base_url() . 'offline' . '/');
        }
    });

    $app->get('/timetable/', function () use($app) {

        $css = ['css/fullcalendar/fullcalendar.css'];
        $js = ['components/modules/fullcalendar/fullcalendar.js'];

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
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        }

        if (!checkStuAccess(get_persondata('personID'))) {
            etsis_redirect(get_base_url() . 'profile' . '/');
        }

        if (_escape(get_option('enable_myetsis_portal')) == 0 && !hasPermission('edit_myetsis_css')) {
            etsis_redirect(get_base_url() . 'offline' . '/');
        }
    });

    $app->get('/terms/', function () use($app) {

        $css = ['css/admin/module.admin.page.alt.tables.min.css'];
        $js = [
            'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
        ];
        try {
            $terms = $app->db->stac()
                    ->select('stac.stuID,stac.termCode,COUNT(stac.termCode) AS Courses,term.id')
                    ->_join('term', 'stac.termCode = term.termCode')
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
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        }

        if (!checkStuAccess(get_persondata('personID'))) {
            etsis_redirect(get_base_url() . 'profile' . '/');
        }

        if (_escape(get_option('enable_myetsis_portal')) == 0 && !hasPermission('edit_myetsis_css')) {
            etsis_redirect(get_base_url() . 'offline' . '/');
        }
    });

    $app->get('/schedule/(\d+)/', function ($id) use($app) {

        $css = ['css/admin/module.admin.page.alt.tables.min.css'];
        $js = [
            'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
        ];

        try {
            $terms = $app->db->course_sec()
                    ->setTableAlias('a')
                    ->select('a.courseSecID,a.courseSecCode,a.secShortTitle,a.startTime,a.termCode')
                    ->select('a.endTime,a.dotw,a.facID,b.buildingName,c.roomNumber,stac.stuID')
                    ->_join('building', 'a.buildingCode = b.buildingCode', 'b')
                    ->_join('room', 'a.roomCode = c.roomCode', 'c')
                    ->_join('stac', 'a.courseSection= stac.courseSection AND a.termCode = stac.termCode')
                    ->_join('term', 'a.termCode = term.termCode')
                    ->where('term.id = ?', $id)->_and_()
                    ->where('stac.stuID = ?', get_persondata('personID'))->_and_()
                    ->whereIn('stac.status', ['A', 'N'])
                    ->groupBy('stac.stuID,stac.courseSection')
                    ->orderBy('stac.id');
            $q = $terms->find(function($data) {
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
         */ elseif (_escape($q[0]['courseSecID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/schedule', [
                'title' => ' Class Schedule',
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
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        }

        if (!checkStuAccess(get_persondata('personID'))) {
            etsis_redirect(get_base_url() . 'profile' . '/');
        }

        if (_escape(get_option('enable_myetsis_portal')) == 0 && !hasPermission('edit_myetsis_css')) {
            etsis_redirect(get_base_url() . 'offline' . '/');
        }
    });

    $app->get('/final-grades/', function () use($app) {

        $css = ['css/admin/module.admin.page.alt.tables.min.css'];
        $js = [
            'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
        ];

        try {
            $final = $app->db->stac()
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

        $app->view->display('student/fgrades', [
            'title' => 'Final Grades',
            'cssArray' => $css,
            'jsArray' => $js,
            'grades' => $q
                ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET', '/deleteSHIS/(\d+)/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }
        if (!hasPermission('delete_student')) {
            _etsis_flash()->error(_t('Permission denied to delete record.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->get('/deleteSHIS/(\d+)/', function ($id) use($app) {
        try {
            $q = $app->db->hiatus()->where('id = ?', $id);
            $q->delete();

            _etsis_flash()->success(_etsis_flash()->notice(200));
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
        etsis_redirect($app->req->server['HTTP_REFERER']);
    });

    /**
     * Before route check.
     */
    $app->before('GET', '/deleteSTAC/(\d+)/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }
        if (!hasPermission('delete_student')) {
            _etsis_flash()->error(_t('Permission denied to delete record.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->get('/deleteSTAC/(\d+)/', function ($id) use($app) {
        try {
            $app->db->query("DELETE 
						a.*,stac.*,stcs.* 
						FROM transfer_credit a 
						LEFT JOIN stac ON a.stacID = stac.id  
						LEFT JOIN stcs ON stac.stuID = stcs.stuID AND stac.courseSecID = stcs.courseSecID 
						WHERE a.stacID = ?", [$id]
            );
            _etsis_flash()->success(_etsis_flash()->notice(200));
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
        etsis_redirect($app->req->server['HTTP_REFERER']);
    });

    /**
     * Before route check.
     */
    $app->before('GET', '/getEvents/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        }
    });

    $app->get('/getEvents/', function () use($app) {
        try {
            $meta = $app->db->event_meta()
                    ->setTableAlias('a')
                    ->select('a.*,b.roomCode,c.buildingCode,e.bgcolor')
                    ->_join('room', 'a.roomCode = b.roomCode', 'b')
                    ->_join('building', 'b.buildingCode = c.buildingCode', 'c')
                    ->_join('event', 'a.eventID = d.eventID', 'd')
                    ->_join('event_category', 'd.catID = e.catID', 'e')
                    ->_join('stac', 'd.termCode = f.termCode AND d.title = f.courseSecCode', 'f')
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
    $app->before('POST', '/progLookup/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
        }
    });

    $app->post('/progLookup/', function () use($app) {
        try {
            $prog = $app->db->acad_program()
                    ->setTableAlias('a')
                    ->select('a.acadProgTitle,a.acadLevelCode,a.schoolCode')
                    ->select('b.majorName,c.locationName,d.schoolName')
                    ->_join('major', 'a.majorCode = b.majorCode', 'b')
                    ->_join('location', 'a.locationCode = c.locationCode', 'c')
                    ->_join('school', 'a.schoolCode = d.schoolCode', 'd')
                    ->where('a.acadProgCode = ?', $app->req->post['acadProgCode'])->_and_()
                    ->where('a.currStatus = "A"')->_and_()
                    ->where('a.endDate IS NULL');
            $q = $prog->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            foreach ($q as $v) {
                $json = [
                    '#acadProgTitle' => $v['acadProgTitle'], '#locationName' => $v['locationName'],
                    "#majorName" => $v['majorName'], "#schoolName" => $v['schoolCode'] . ' ' . $v['schoolName'],
                    "#acadLevelCode" => $v['acadLevelCode']
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
    $app->before('GET|POST', '/stuLookup/', function() {
        if (!is_user_logged_in()) {
            _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
            exit();
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
                    'id' => _escape($x->stuID),
                    'label' => get_name(_escape($x->stuID)),
                    'value' => get_name(_escape($x->stuID))
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

    $app->setError(function() use($app) {

        $app->view->display('error/404', ['title' => '404 Error']);
    });
});

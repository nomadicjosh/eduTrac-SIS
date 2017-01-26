<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;

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
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $post = $app->req->post['spro'];
                $spro = $app->db->student()
                    ->setTableAlias('a')
                    ->select('a.stuID,b.lname,b.fname,b.email,b.altID')
                    ->_join('person', 'a.stuID = b.personID', 'b')
                    ->whereLike('CONCAT(b.fname," ",b.lname)', "%$post%")->_or_()
                    ->whereLike('CONCAT(b.lname," ",b.fname)', "%$post%")->_or_()
                    ->whereLike('CONCAT(b.lname,", ",b.fname)', "%$post%")->_or_()
                    ->whereLike('b.uname', "%$post%")->_or_()
                    ->whereLike('b.altID', "%$post%")->_or_()
                    ->whereLike('a.stuID', "%$post%");

                $q = $spro->find(function($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('student/index', [
            'title' => 'Student Search',
            'search' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {
        if ($app->req->isPost()) {
            try {
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
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        try {
            $spro = $app->db->student()->where('stuID', $id)->findOne();
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }

        $json = _file_get_contents($json_url . 'application/personID/' . (int) $id . '/?key=' . get_option('api_key'));
        $admit = json_decode($json, true);

        try {
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
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
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
        if (!hasPermission('create_stu_record')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow, $email) {
        if ($app->req->isPost()) {
            try {
                $nae = get_person_by('personID', $id);
                if ($nae->ssn > 0) {
                    $pass = str_replace('-', '', $nae->ssn);
                } elseif ($nae->dob != '0000-00-00') {
                    $pass = str_replace('-', '', $nae->dob);
                } else {
                    $pass = 'myaccount';
                }
                $degree = $app->db->acad_program()->where('acadProgCode = ?', _trim($app->req->post['acadProgCode']))->findOne();
                $appl = $app->db->application()->where('personID = ?', $id)->findOne();

                $student = $app->db->student();
                $student->insert([
                    'stuID' => $id,
                    'status' => $app->req->post['status'],
                    'addDate' => $app->db->NOW(),
                    'approvedBy' => get_persondata('personID')
                ]);

                $sacp = $app->db->stu_program();
                $sacp->insert([
                    'stuID' => $id,
                    'acadProgCode' => _trim($app->req->post['acadProgCode']),
                    'currStatus' => 'A',
                    'statusDate' => $app->db->NOW(),
                    'startDate' => $app->req->post['startDate'],
                    'approvedBy' => get_persondata('personID'),
                    'antGradDate' => $app->req->post['antGradDate'],
                    'advisorID' => $app->req->post['advisorID'],
                    'catYearCode' => _trim($app->req->post['catYearCode'])
                ]);

                $al = $app->db->stu_acad_level();
                $al->insert([
                    'stuID' => $id,
                    'acadProgCode' => _trim($app->req->post['acadProgCode']),
                    'acadLevelCode' => _trim($app->req->post['acadLevelCode']),
                    'addDate' => $app->db->NOW()
                ]);

                /**
                 * Fires before new student record is created.
                 *
                 * @since 6.1.07
                 * @param int $id Student's ID.
                 */
                $app->hook->do_action('pre_save_stu', $id);

                $student->save();
                $sacp->save();
                $al->save();

                if (_h(get_option('send_acceptance_email')) == 1) {
                    try {
                        if (!Validate::table('acceptance_letter')->exists()) {
                            // Creates node's schema if does not exist.
                            Node::dispense('acceptance_letter');
                        }

                        $node = Node::table('acceptance_letter');
                        $node->personid = (int) $id;
                        $node->uname = (string) $nae->uname;
                        $node->fname = (string) $nae->fname;
                        $node->lname = (string) $nae->lname;
                        $node->name = (string) get_name($id);
                        $node->email = (string) $nae->email;
                        $node->sacp = (string) _trim($app->req->post['acadProgCode']);
                        $node->acadlevel = (string) _trim($app->req->post['acadLevelCode']);
                        $node->degree = (string) $degree->degreeCode;
                        $node->startterm = (string) $appl->startTerm;
                        $node->sent = (int) 0;
                        $node->save();
                    } catch (NodeQException $e) {
                        _etsis_flash()->error($e->getMessage());
                    } catch (Exception $e) {
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
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        try {
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
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
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

            redirect(get_base_url() . 'stu' . '/' . $id . '/');
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_script('select');
            etsis_register_script('select2');

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
        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/stac/(\d+)/', function ($id) use($app, $css, $js, $json_url) {

        try {
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
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
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
        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/sttr/(\d+)/', function ($id) use($app, $css, $js, $json_url) {

        try {
            $sttr = $app->db->sttr()
                ->select('sttr.termCode,sttr.acadLevelCode,sttr.attCred,sttr.compCred')
                ->select('sttr.stuID,sttr.gradePoints,sttr.gpa,sttr.stuLoad')
                ->select('b.termStartDate,b.termEndDate')
                ->_join('term', 'sttr.termCode = b.termCode', 'b')
                ->_join('stu_course_sec', 'sttr.termCode = c.termCode AND sttr.stuID = c.stuID', 'c')
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
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
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
        if (!hasPermission('access_student_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/shis/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {

        if ($app->req->isPost()) {
            try {
                if (isset($app->req->post['shisID'])) {
                    $size = count($app->req->post['shisID']);
                    $i = 0;
                    while ($i < $size) {
                        $shis = $app->db->hiatus();
                        $shis->shisCode = $app->req->post['shisCode'][$i];
                        $shis->startDate = $app->req->post['startDate'][$i];
                        $shis->endDate = $app->req->post['endDate'][$i];
                        $shis->comment = $app->req->post['comment'][$i];
                        $shis->where('stuID = ?', $id)->_and_()->where('shisID = ?', $app->req->post['shisID'][$i]);
                        $shis->update();
                        ++$i;

                        etsis_cache_delete($id, 'stu');
                        etsis_logger_activity_log_write('Update Record', 'Student Hiatus', get_name($id), get_persondata('uname'));
                        _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                    }
                } else {
                    $shis = $app->db->hiatus();
                    foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                        $shis->$k = $v;
                    }
                    $shis->save();

                    etsis_cache_delete($id, 'stu');
                    etsis_logger_activity_log_write('New Record', 'Student Hiatus (SHIS)', get_name($id), get_persondata('uname'));
                    _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        $json = _file_get_contents($json_url . 'student/stuID/' . $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);

        try {
            $shis = $app->db->query("SELECT 
                    CASE shisCode 
                	WHEN 'W' THEN 'Withdrawal'
                	WHEN 'LOA' THEN 'Leave of Absence'
                	WHEN 'SA' THEN 'Study Abroad'
                	WHEN 'ILL' THEN 'Illness'
                	ELSE 'Dismissed'
                	END AS 'Code',
                	shisID,stuID,shisCode,startDate,endDate,comment,
                    Case WHEN comment IS NULL or comment = '' THEN 'empty' ELSE comment END AS Comment
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
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
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
         */ elseif (count($decode[0]['stuID']) <= 0) {

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

            $app->view->display('student/shis', [
                'title' => get_name($id),
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
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/strc/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {

        if ($app->req->isPost()) {
            try {
                if (isset($app->req->post['rstrID'])) {
                    $size = count($app->req->post['rstrID']);
                    $i = 0;
                    while ($i < $size) {
                        $strc = $app->db->restriction();
                        $strc->rstrCode = $app->req->post['rstrCode'][$i];
                        $strc->severity = $app->req->post['severity'][$i];
                        $strc->startDate = $app->req->post['startDate'][$i];
                        $strc->endDate = $app->req->post['endDate'][$i];
                        $strc->comment = $app->req->post['comment'][$i];
                        $strc->where('stuID = ?', $id)->_and_()->where('rstrID = ?', $app->req->post['rstrID'][$i]);
                        $strc->update();
                        ++$i;

                        etsis_logger_activity_log_write('Update Record', 'Student Restriction', get_name($id), get_persondata('uname'));
                    }
                } else {
                    $strc = $app->db->restriction();
                    foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                        $strc->$k = $v;
                    }
                    $strc->save();
                    etsis_logger_activity_log_write('New Record', 'Student Restriction (STRC)', get_name($id), get_persondata('uname'));
                }
                etsis_cache_delete($id, 'stu');
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        $json = _file_get_contents($json_url . 'student/stuID/' . $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);

        try {
            $strc = $app->db->query("SELECT 
                        a.*,b.deptCode,
                        Case WHEN a.comment IS NULL or a.comment = '' THEN 'empty' ELSE a.comment END AS Comment
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
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
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
         */ elseif (count($decode[0]['stuID']) <= 0) {

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

            $app->view->display('student/strc', [
                'title' => get_name($id),
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
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/sacd/(\d+)/', function ($id) use($app, $css, $js, $json_url) {

        $json = _file_get_contents($json_url . 'stu_acad_cred/stuAcadCredID/' . (int) $id . '/?key=' . get_option('api_key'));
        $decode = json_decode($json, true);

        $date = date("Y-m-d");
        $time = date("h:m A");

        if ($app->req->isPost()) {
            try {
                $rterm = _file_get_contents($json_url . 'term/termCode/' . $app->req->post['termCode'] . '/?key=' . get_option('api_key'));
                $term = json_decode($rterm, true);

                $detail = $app->db->stu_acad_cred();
                $detail->courseID = $app->req->post['courseID'];
                $detail->courseSecID = $decode[0]['courseSecID'];
                $detail->courseCode = $app->req->post['courseCode'];
                $detail->courseSecCode = $decode[0]['courseSecCode'];
                $detail->sectionNumber = $app->req->post['sectionNumber'];
                $detail->courseSection = $decode[0]['courseSection'];
                $detail->termCode = $app->req->post['termCode'];
                $detail->reportingTerm = $term[0]['reportingTerm'];
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
                $detail->endDate = $app->req->post['endDate'];
                if (($app->req->post['status'] == 'W' || $app->req->post['status'] == 'D') && $date >= $term[0]['termStartDate'] && $date > $term[0]['dropAddEndDate']) {
                    $detail->compCred = '0.0';
                    $detail->gradePoints = acadCredGradePoints($app->req->post['grade'], '0.0');
                    $detail->statusTime = $time;
                    if (empty($app->req->post['grade'])) {
                        $detail->grade = "W";
                    } else {
                        $detail->grade = $app->req->post['grade'];
                    }
                } else {
                    if (acadCredGradePoints($app->req->post['grade'], $app->req->post['attCred']) > 0) {
                        $compCred = $app->req->post['attCred'];
                    } else {
                        $compCred = '0';
                    }
                    $detail->compCred = $compCred;
                    $detail->gradePoints = acadCredGradePoints($app->req->post['grade'], $app->req->post['attCred']);
                    $detail->grade = $app->req->post['grade'];
                }
                $detail->where('stuAcadCredID = ?', $id);

                /**
                 * If the posted status is 'W' or 'D' and today's date is less than the 
                 * primary term start date, then delete all student course sec as well as 
                 * student acad cred records.
                 */
                if (($app->req->post['status'] == 'W' || $app->req->post['status'] == 'D') && $date < $term[0]['termStartDate']) {
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
                        $stuTuition = $app->db->stu_acct_tuition()->where('stuID = ? AND termCode = ?', [$decode[0]['stuID'], $app->req->post['termCode']])->findOne();
                        $q = $app->db->stu_acct_tuition();
                        $q->total = bcsub($stuTuition->total, $total);
                        $q->where('stuID = ?', $decode[0]['stuID'])->_and_()->where('termCode = ?', $app->req->post['termCode'])->update();
                        /**
                         * End updating tuition totals.
                         */
                    }
                    _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'stu/stac' . '/' . $decode[0]['stuID'] . '/');
                    return;
                }
                /**
                 * If posted status is 'W' or 'D' and today's date is greater than equal to the 
                 * primary term start date, and today's date is less than the term's drop/add 
                 * end date, then delete all student course sec as well as student acad cred 
                 * records.
                 */ elseif (($app->req->post['status'] == 'W' || $app->req->post['status'] == 'D') && $date >= $term[0]['termStartDate'] && $date < $term[0]['dropAddEndDate']) {
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
                        $q->where('stuID = ?', $decode[0]['stuID'])->_and_()->where('termCode = ?', $app->req->post['termCode'])->update();
                        /**
                         * End updating tuition totals.
                         */
                    }
                    _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'stu/stac' . '/' . $decode[0]['stuID'] . '/');
                    return;
                }
                /**
                 * If posted status is 'W' or 'D' and today's date is greater than equal to the 
                 * primary term start date, and today's date is greater than the term's drop/add 
                 * end date, then update student course sec record with a 'W' status and update  
                 * student acad record with a 'W' grade and 0.0 completed credits.
                 */ elseif (($app->req->post['status'] == 'W' || $app->req->post['status'] == 'D') && $date >= $term[0]['termStartDate'] && $date > $term[0]['dropAddEndDate']) {
                    $q = $app->db->stu_course_sec();
                    $q->courseSecCode = $app->req->post['courseSecCode'];
                    $q->termCode = $app->req->post['termCode'];
                    $q->courseCredits = $app->req->post['attCred'];
                    $q->status = $app->req->post['status'];
                    $q->statusDate = $q->NOW();
                    $q->statusTime = $time;
                    $q->where('stuID = ?', $decode[0]['stuID'])->_and_()
                        ->where('courseSecID = ?', $app->req->post['courseSecID'])
                        ->update();
                    $detail->update();
                }
                /**
                 * If there is no status change or the status change is not a 'W', 
                 * just update stu_course_sec and stu_acad_cred records with the 
                 * changed information.
                 */ else {
                    $q = $app->db->stu_course_sec();
                    $q->courseSecCode = $app->req->post['courseSecCode'];
                    $q->termCode = $app->req->post['termCode'];
                    $q->courseCredits = $app->req->post['attCred'];
                    $q->status = $app->req->post['status'];
                    $q->statusDate = $app->req->post['statusDate'];
                    $q->statusTime = $app->req->post['statusTime'];
                    $q->where('stuID = ?', $decode[0]['stuID'])->_and_()
                        ->where('courseSecID = ?', $app->req->post['courseSecID'])
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
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
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
         */ elseif (count($decode) <= 0) {

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
                'title' => get_name($decode[0]['stuID']),
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
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/sacp/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {
        if ($app->req->isPost()) {
            try {
                $sacp = $app->db->stu_program();
                foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                    $sacp->$k = $v;
                }
                $sacp->where('stuProgID = ?', $id);
                $sacp->update();
                etsis_logger_activity_log_write('Update Record', 'Student Acad Program (SACP)', get_name($app->req->post['stuID']), get_persondata('uname'));
                etsis_cache_delete($id, 'stu');
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        try {
            $sacp = $app->db->acad_program()
                ->setTableAlias('a')
                ->select('a.acadProgCode,a.schoolCode,a.acadLevelCode,b.stuProgID')
                ->select('b.eligible_to_graduate,b.graduationDate,b.antGradDate')
                ->select('b.stuID,b.advisorID,b.catYearCode,b.currStatus')
                ->select('b.statusDate,b.startDate,b.endDate,b.comments')
                ->select('b.approvedBy,b.LastUpdate,c.schoolName')
                ->select('Case WHEN b.comments IS NULL or b.comments = "" THEN "empty" ELSE b.comments END AS Comment')
                ->_join('stu_program', 'a.acadProgCode = b.acadProgCode', 'b')
                ->_join('school', 'a.schoolCode = c.schoolCode', 'c')
                ->where('b.stuProgID = ?', $id);
            $q = $sacp->findOne();
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
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
                'title' => get_name($q->stuID),
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
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/add-prog/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {
        if ($app->req->isPost()) {
            $json = _file_get_contents($json_url . 'acad_program/acadProgCode/' . $app->req->post['acadProgCode'] . '/?key=' . get_option('api_key'));
            $decode = json_decode($json, true);

            try {
                $level = $app->db->stu_acad_level()
                    ->where('stuID = ?', $id)->_and_()
                    ->where('acadProgCode = ?', $app->req->post['acadProgCode'])
                    ->findOne();

                $sacp = $app->db->stu_program();
                $sacp->stuID = $id;
                $sacp->acadProgCode = _trim($app->req->post['acadProgCode']);
                $sacp->currStatus = $app->req->post['currStatus'];
                $sacp->statusDate = $app->db->NOW();
                $sacp->startDate = $app->req->post['startDate'];
                $sacp->endDate = $app->req->post['endDate'];
                $sacp->approvedBy = get_persondata('personID');
                $sacp->antGradDate = $app->req->post['antGradDate'];
                $sacp->advisorID = $app->req->post['advisorID'];
                $sacp->catYearCode = $app->req->post['catYearCode'];
                $sacp->save();
                if (count($level->id) <= 0) {
                    $al = $app->db->stu_acad_level();
                    $al->stuID = $id;
                    $al->acadProgCode = _trim($app->req->post['acadProgCode']);
                    $al->acadLevelCode = $decode[0]['acadLevelCode'];
                    $al->addDate = $app->db->NOW();
                    $al->save();
                }
                etsis_logger_activity_log_write('New Record', 'Student Academic Program', get_name($id), get_persondata('uname'));
                etsis_cache_delete($id, 'stu');
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'stu' . '/' . $id . '/');
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        try {
            $stu = $app->db->student()->where('stuID = ?', $id);
            $q = $stu->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
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

            $app->view->display('student/add-prog', [
                'title' => get_name($id),
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
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/graduation/', function () use($app, $css, $js, $flashNow) {
        if ($app->req->isPost()) {
            try {
                if (!empty($app->req->post['studentID'])) {
                    $grad = $app->db->stu_program();
                    $grad->statusDate = $grad->NOW();
                    $grad->endDate = $grad->NOW();
                    $grad->currStatus = 'G';
                    $grad->graduationDate = $app->req->post['gradDate'];
                    $grad->where('stuID = ?', $app->req->post['studentID'])->_and_()->where('eligible_to_graduate = "1"');
                    $grad->update();
                    _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                } else {
                    $grad = $app->db->graduation_hold();
                    $grad->queryID = $app->req->post['queryID'];
                    $grad->gradDate = $app->req->post['gradDate'];
                    $grad->save();
                    etsis_logger_activity_log_write('Update Record', 'Graduation', get_name($app->req->post['stuID']), get_persondata('uname'));
                    _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        etsis_register_style('form');
        etsis_register_script('select');
        etsis_register_script('select2');
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
        if (!hasPermission('generate_transcripts')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/tran/', function () use($app, $css, $js) {
        if ($app->req->isPost()) {
            redirect(get_base_url() . 'stu/tran' . '/' . $app->req->post['stuID'] . '/' . $app->req->post['acadLevelCode'] . '/' . $app->req->post['template'] . '/');
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

    $app->get('/tran/(\d+)/(\w+)/(\w+)/', function ($id, $level, $template) use($app, $css, $js, $flashNow) {
        try {
            $tranInfo = $app->db->stu_acad_cred()
                ->setTableAlias('a')
                ->select('CASE a.acadLevelCode WHEN "UG" THEN "Undergraduate" WHEN "GR" THEN "Graduate" '
                    . 'WHEN "Phd" THEN "Doctorate" WHEN "CE" THEN "Continuing Education" WHEN "CTF" THEN "Certificate" '
                    . 'WHEN "DIP" THEN "Diploma" WHEN "PR" THEN "Professional" ELSE "Non-Degree" END AS "Level"')
                ->select('a.stuID,b.address1,b.address2,b.city,b.state')
                ->select('b.zip,c.ssn,c.dob,c.altID,d.graduationDate,f.degreeCode')
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
                ->where('(a.stuID = ? OR c.altID = ?)', [$id, $id])->_and_()
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
                        stac.startDate,stac.endDate,person.altID 
                    FROM stu_acad_cred stac
                    LEFT JOIN term ON stac.termCode = term.termCode
                    LEFT JOIN person ON stac.stuID = person.personID
                    WHERE (stac.stuID = ? OR person.altID = ?) 
                    AND stac.acadLevelCode = ? 
                    AND stac.creditType = 'I' 
                    GROUP BY stac.courseSecCode,stac.termCode,stac.acadLevelCode
                    ORDER BY term.termStartDate ASC", [$id, $id, $level]);
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
                ->select('SUM(stac.gradePoints)/SUM(stac.attCred) as GPA,person.altID')
                ->_join('person','stac.stuID = person.personID')
                ->where('(stac.stuID = ? OR person.altID = ?)', [$id, $id])->_and_()
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
                        stac.compCred,stac.attCred,stac.grade,stac.gradePoints,
                        stac.termCode,stac.creditType,
                        stac.shortTitle,REPLACE(stac.courseCode,'-',' ') AS CourseName,stac.courseSecCode,
                        person.altID 
                    FROM stu_acad_cred AS stac 
                    LEFT JOIN person ON stac.stuID = person.personID 
                    WHERE (stac.stuID = ? OR person.altID = ?) 
                    AND stac.acadLevelCode = ? 
                    AND stac.creditType = 'TR'", [$id, $id, $level]);
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
                ->select('SUM(stac.gradePoints)/SUM(stac.attCred) as GPA,person.altID')
                ->_join('person','stac.stuID = person.personID')
                ->where('(stac.stuID = ? OR person.altID = ?)', [$id, $id])->_and_()
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
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
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
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(get_base_url() . 'profile' . '/');
        }

        if (_h(get_option('enable_myet_portal')) == 0 && !hasPermission('edit_myet_css')) {
            redirect(get_base_url() . 'offline' . '/');
        }
    });

    $app->get('/timetable/', function () use($app) {

        $css = [ 'css/fullcalendar/fullcalendar.css', 'css/fullcalendar/calendar.css'];
        $js = [ 'components/modules/fullcalendar/fullcalendar.js'];

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
        try {
            $q = $app->db->hiatus()->where('shisID = ?', $id);

            $q->delete();
            _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            redirect($app->req->server['HTTP_REFERER']);
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
        }
    });

    $app->get('/deleteSTAC/(\d+)/', function ($id) use($app, $flashNow) {
        try {
            $q = $app->db->query("DELETE 
						a.*,b.*,c.* 
						FROM transfer_credit a 
						LEFT JOIN stu_acad_cred b ON a.stuAcadCredID = b.stuAcadCredID  
						LEFT JOIN stu_course_sec c ON b.stuID = c.stuID AND b.courseSecID = c.courseSecID 
						WHERE a.stuAcadCredID = ?", [$id]
            );

            if ($q) {
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
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
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }
    });

    $app->post('/progLookup/', function () use($app, $flashNow) {
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
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }
    });

    $app->match('GET|POST', '/stu/paypal-ipn/', function () use($app) {

        $app->view->display('student/paypal-ipn', [
            'title' => 'Paypal IPN'
        ]);
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/stu/payment-cancel/', function () {
        if (!is_user_logged_in()) {
            redirect(get_base_url() . 'login' . '/');
        }
    });

    $app->match('GET|POST', '/stu/payment-cancel/', function () use($app) {

        $app->view->display('student/payment-cancel', [
            'title' => 'Payment Cancelled'
        ]);
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/stu/payment-success/', function () {
        if (!is_user_logged_in()) {
            redirect(get_base_url() . 'login' . '/');
        }
    });

    $app->match('GET|POST', '/stu/payment-success/', function () use($app) {

        $app->view->display('student/payment-success', [
            'title' => 'Payment Successful'
        ]);
    });

    $app->post('/stu/redirectPaypal/', function () use($app) {

        $vars = [];
        $vars['amount'] = $app->req->_post('amount');
        $vars['cmd'] = $app->req->_post('cmd');
        $vars['business'] = $app->req->_post('business');
        $vars['currency_code'] = $app->req->_post('currency_code');
        $vars['item_name'] = $app->req->_post('item_name');
        $vars['return'] = $app->req->_post('return');
        $vars['notify_url'] = $app->req->_post('notify_url');
        $vars['cancel_return'] = $app->req->_post('cancel_return');
        $vars['custom'] = $app->req->_post('custom');
        $vars['item_number'] = $app->req->_post('item_number');
        $vars['shipping'] = $app->req->_post('shipping');
        etsis_cache_flush_namespace('student_account');
        redirect($app->req->_post('ppurl') . '?' . http_build_query($vars));
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/stu/bill/', function () {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(get_base_url() . 'profile' . '/');
        }

        if (_h(get_option('enable_myet_portal')) == 0 && !hasPermission('edit_myet_css')) {
            redirect(get_base_url() . 'offline' . '/');
        }
    });

    $app->get('/stu/bill/', function () use($app) {

        $css = [
            'css/admin/module.admin.page.alt.tables.min.css'
        ];
        $js = [
            'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
        ];

        try {
            $bill = $app->db->stu_acct_bill()
                ->select('billID,stuID,termCode')
                ->where('stuID = ?', get_persondata('personID'))
                ->groupBy('stuID,termCode');
            $q = etsis_cache_get('students_bill' . get_persondata('personID'), 'student_account');
            if (empty($q)) {
                $q = $bill->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('students_bill' . get_persondata('personID'), $q, 'student_account');
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }

        $app->view->display('student/bill', [
            'title' => 'My Bills',
            'cssArray' => $css,
            'jsArray' => $js,
            'bill' => $q
        ]);
    });

    $app->match('GET|POST', '/stu/bill/([^/]+)/', function ($id) use($app) {
        try {
            $bill = $app->db->stu_acct_bill()
                ->setTableAlias('a')
                ->select('a.*, b.termName')
                ->_join('term', 'a.termCode = b.termCode', 'b')
                ->where('billID = ?', $id)
                ->_and_()
                ->where('stuID = ?', get_persondata('personID'));
            $q1 = etsis_cache_get('bill' . $id, 'student_account');
            if (empty($q1)) {
                $q1 = $bill->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('bill' . $id, $q1, 'student_account');
            }
            $stuTuition = $app->db->stu_acct_tuition()
                ->where('stuID = ?', $q1[0]['stuID'])
                ->_and_()
                ->where('termCode = ?', $q1[0]['termCode']);
            $query = etsis_cache_get('stuTuition' . $id, 'student_account');
            if (empty($query)) {
                $query = $stuTuition->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('stuTuition' . $id, $query, 'student_account');
            }
            $tuition = $app->db->query('SELECT ' . 'SUM(amount) as sum ' . 'FROM stu_acct_fee ' . 'WHERE billID = ? ' . 'AND type = "Tuition" ' . 'GROUP BY stuID, termCode', [
                $id
            ]);
            $q2 = etsis_cache_get('tuition' . $id, 'student_account');
            if (empty($q2)) {
                $q2 = $tuition->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('tuition' . $id, $q2, 'student_account');
            }
            $fee = $app->db->query('SELECT ' . 'ID, description, amount as Fee ' . 'FROM stu_acct_fee ' . 'WHERE billID = ? ' . 'AND type = "Fee"', [
                $id
            ]);
            $q3 = etsis_cache_get('fee' . $id, 'student_account');
            if (empty($q3)) {
                $q3 = $fee->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('fee' . $id, $q3, 'student_account');
            }
            $sumFee = $app->db->query("SELECT 
                        SUM(amount) as sum 
                    FROM stu_acct_fee 
                    WHERE billID = ? 
                    AND type = 'Fee' 
                    GROUP BY stuID,termCode", [
                $id
            ]);
            $q4 = etsis_cache_get('sumFee' . $id, 'student_account');
            if (empty($q4)) {
                $q4 = $sumFee->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('sumFee' . $id, $q4, 'student_account');
            }
            $sumPay = $app->db->query("SELECT 
                        SUM(amount) as sum 
                    FROM payment 
                    WHERE stuID = ? 
                    AND termCode = ? 
                    GROUP BY stuID,termCode", [
                $q1[0]['stuID'],
                $q1[0]['termCode']
            ]);
            $q5 = etsis_cache_get('sumPay' . $id, 'student_account');
            if (empty($q5)) {
                $q5 = $sumPay->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('sumPay' . $id, $q5, 'student_account');
            }
            $sumRefund = $app->db->query("SELECT 
                        SUM(amount) as sum 
                    FROM refund 
                    WHERE stuID = ? 
                    AND termCode = ? 
                    GROUP BY stuID,termCode", [
                $q1[0]['stuID'],
                $q1[0]['termCode']
            ]);
            $q6 = etsis_cache_get('sumRefund' . $id, 'student_account');
            if (empty($q6)) {
                $q6 = $sumRefund->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('sumRefund' . $id, $q6, 'student_account');
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($q1 == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q1) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($q1) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('student/vbill', [
                'title' => $q1[0]['termCode'] . ' Bill',
                'bill' => $q1,
                'tuition1' => $query,
                'tuition2' => money_format('%n', $q2[0]['sum']),
                'fee' => $q3,
                'begin' => money_format('-%n', bcadd($query[0]['total'], $q4[0]['sum'])),
                'sumFee' => $q4[0]['sum'],
                'sumPayments' => $q5[0]['sum'],
                'sumRefund' => $q6[0]['sum']
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/stu/account-history/', function () use($app) {
        if (!checkStuAccess(get_persondata('personID'))) {
            redirect(get_base_url() . 'profile' . '/');
        }

        if (_h(get_option('enable_myet_portal')) == 0 && !hasPermission('edit_myet_css')) {
            redirect(get_base_url() . 'offline' . '/');
        }
    });

    $app->get('/stu/account-history/', function () use($app, $js, $flashNow) {
        $css = [
            'css/admin/module.admin.page.alt.tables.min.css'
        ];
        $js = [
            'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
            'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
        ];
        try {
            $plan = $app->db->query('SELECT *,' . ' CASE payFrequency' . ' WHEN "1" THEN "Daily"' . ' WHEN "7" THEN "Weekly"' . ' WHEN "14" THEN "Bi-Weekly"' . ' WHEN "30" THEN "Monthly"' . ' ELSE "Yearly"' . ' END AS Frequency' . ' FROM stu_acct_pp' . ' WHERE stuID = ?', [
                get_persondata('personID')
            ]);
            $sql = etsis_cache_get('payment_plan' . get_persondata('personID'), 'student_account');
            if (empty($sql)) {
                $sql = $plan->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('payment_plan' . get_persondata('personID'), $sql, 'student_account');
            }
            $history = $app->db->query('SELECT 
            ID AS FeeID, billID AS billID, stuID AS stuID,
            termCode AS termCode, type as type, description AS description,
            amount AS FeeAmount, NULL AS PayAmount, NULL AS method, feeDate AS dateStamp 
            FROM stu_acct_fee 
            WHERE stuID = ? 
            AND type = "Fee"
            UNION ALL 
                SELECT NULL as FeeID, NULL AS billID, stuID AS stuID, termCode AS termCode,
                NULL as type, "Tuition" as description, total AS FeeAmount, NULL AS PayAmount,
                NULL as method, tuitionTimeStamp AS dateStamp 
                FROM stu_acct_tuition 
                WHERE stuID = ? 
            UNION ALL 
            SELECT NULL as FeeID, NULL AS billID, stuID AS stuID, termCode AS termCode,
            NULL as type, "Payment" as description, NULL AS FeeAmount, amount AS PayAmount, 
            paymentTypeID as method, paymentDate AS dateStamp 
            FROM payment 
            WHERE stuID = ? 
            ORDER BY dateStamp ASC', [
                get_persondata('personID'),
                get_persondata('personID'),
                get_persondata('personID')
            ]);
            $q = etsis_cache_get('history' . get_persondata('personID'), 'student_account');
            if (empty($q)) {
                $q = $history->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('history' . get_persondata('personID'), $q, 'student_account');
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($q === false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) === true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($q[0]['stuID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {
            $app->view->display('student/account-history', [
                'title' => 'Student Account History',
                'cssArray' => $css,
                'jsArray' => $js,
                'history' => $q,
                'plan' => $sql
            ]);
        }
    });

    $app->setError(function() use($app) {

        $app->view->display('error/404', ['title' => '404 Error']);
    });
});

<?php
if (! defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Form Router
 *
 * @license GPLv3
 *         
 * @since 5.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

$logger = new \app\src\Log();
$flashNow = new \app\src\Messages();

$css = [
    'css/admin/module.admin.page.form_elements.min.css',
    'css/admin/module.admin.page.tables.min.css'
];
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
    'components/modules/admin/forms/elements/jasny-fileupload/assets/js/bootstrap-fileupload.js?v=v2.1.0'
];

$app->group('/form', 
    function () use($app, $css, $js, $logger, $dbcache, $flashNow) {
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/semester/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/semester/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $sem = $app->db->semester();
                foreach ($_POST as $k => $v) {
                    $sem->$k = $v;
                }
                if ($sem->save()) {
                    $ID = $sem->lastInsertId();
                    etsis_cache_flush_namespace('sem');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Semester', _filter_input_string(INPUT_POST, 'semName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'semCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/semester' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $sem = $app->db->semester()
                ->whereNot('semCode', 'NULL')
                ->orderBy('acadYearCode', 'DESC');
            
            $q = etsis_cache_get('sem', 'sem');
            if (empty($q)) {
                $q = $sem->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('sem', $q, 'sem');
            }
            
            $app->view->display('form/semester', [
                'title' => 'Semester',
                'cssArray' => $css,
                'jsArray' => $js,
                'semester' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/semester/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        $app->match('GET|POST', '/semester/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $sem = $app->db->semester();
                foreach ($_POST as $k => $v) {
                    $sem->$k = $v;
                }
                $sem->where('semesterID = ?', $id);
                if ($sem->update()) {
                    etsis_cache_flush_namespace('sem');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Semester', _filter_input_string(INPUT_POST, 'semName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'semCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $sem = $app->db->semester()
                ->where('semesterID = ?', $id);
            
            $q = etsis_cache_get($id, 'sem');
            if (empty($q)) {
                $q = $sem->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'sem');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['semesterID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-semester', [
                    'title' => 'View Semester',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'semester' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/term/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/term/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $term = $app->db->term();
                foreach ($_POST as $k => $v) {
                    $term->$k = $v;
                }
                if ($term->save()) {
                    $ID = $term->lastInsertId();
                    etsis_cache_flush_namespace('term');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Term', _filter_input_string(INPUT_POST, 'termName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'termCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/term' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $term = $app->db->term()
                ->select('term.*,semester.semName')
                ->_join('semester', 'term.semCode = semester.semCode')
                ->whereNot('term.termCode', 'NULL')
                ->orderBy('term.termCode', 'DESC');
            
            $q = etsis_cache_get('term', 'term');
            if (empty($q)) {
                $q = $term->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('term', $q, 'term');
            }
            
            $app->view->display('form/term', [
                'title' => 'Term',
                'cssArray' => $css,
                'jsArray' => $js,
                'term' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/term/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        $app->match('GET|POST', '/term/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $term = $app->db->term();
                foreach ($_POST as $k => $v) {
                    $term->$k = $v;
                }
                $term->where('termID = ?', $id);
                if ($term->update()) {
                    etsis_cache_flush_namespace('term');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Term', _filter_input_string(INPUT_POST, 'termName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'termCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $term = $app->db->term()
                ->where('termID = ?', $id);
            
            $q = etsis_cache_get($id, 'term');
            if (empty($q)) {
                $q = $term->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'term');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['termID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-term', [
                    'title' => 'View Term',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'term' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/acad-year/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/acad-year/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $year = $app->db->acad_year();
                foreach ($_POST as $k => $v) {
                    $year->$k = $v;
                }
                if ($year->save()) {
                    $ID = $year->lastInsertId();
                    etsis_cache_flush_namespace('ayr');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Academic Year', _filter_input_string(INPUT_POST, 'acadYearDesc'), get_persondata('uname'));
                    redirect(get_base_url() . 'form/acad-year' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $ay = $app->db->acad_year()
                ->whereNot('acadYearCode', 'NULL')
                ->orderBy('acadYearCode', 'DESC');
            
            $q = etsis_cache_get('ayr', 'ayr');
            if (empty($q)) {
                $q = $ay->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('ayr', $q, 'ayr');
            }
            
            $app->view->display('form/acad-year', [
                'title' => 'Academic Year',
                'cssArray' => $css,
                'jsArray' => $js,
                'acadYear' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/acad-year/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/acad-year/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $year = $app->db->acad_year();
                foreach ($_POST as $k => $v) {
                    $year->$k = $v;
                }
                $year->where('acadYearID = ?', $id);
                if ($year->update()) {
                    etsis_cache_flush_namespace('ayr');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Academic Year', _filter_input_string(INPUT_POST, 'acadYearDesc'), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $ay = $app->db->acad_year()
                ->where('acadYearID = ?', $id);
            
            $q = etsis_cache_get($id, 'ayr');
            if (empty($q)) {
                $q = $ay->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'ayr');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['acadYearID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-acad-year', [
                    'title' => 'View Acad Year',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'acadYear' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/department/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/department/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $dept = $app->db->department();
                foreach ($_POST as $k => $v) {
                    $dept->$k = $v;
                }
                if ($dept->save()) {
                    $ID = $dept->lastInsertId();
                    etsis_cache_flush_namespace('dept');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Department', _filter_input_string(INPUT_POST, 'deptName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'deptCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/department' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $dept = $app->db->department()
                ->whereNot('deptCode', 'NULL')
                ->orderBy('deptCode');
            
            $q = etsis_cache_get('dept', 'dept');
            if (empty($q)) {
                $q = $dept->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('dept', $q, 'dept');
            }
            
            $app->view->display('form/department', [
                'title' => 'Department',
                'cssArray' => $css,
                'jsArray' => $js,
                'dept' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/department/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/department/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $dept = $app->db->department();
                foreach ($_POST as $k => $v) {
                    $dept->$k = $v;
                }
                $dept->where('deptID = ?', $id);
                if ($dept->update()) {
                    etsis_cache_flush_namespace('dept');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Department', _filter_input_string(INPUT_POST, 'deptName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'deptCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $dept = $app->db->department()
                ->where('deptID = ?', $id);
            
            $q = etsis_cache_get($id, 'dept');
            if (empty($q)) {
                $q = $dept->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'dept');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['deptID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-department', [
                    'title' => 'View Department',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'dept' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/subject/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/subject/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $subj = $app->db->subject();
                $subj->subjectCode = _filter_input_string(INPUT_POST, 'subjectCode');
                $subj->subjectName = _filter_input_string(INPUT_POST, 'subjectName');
                if ($subj->save()) {
                    $ID = $subj->lastInsertId();
                    etsis_cache_flush_namespace('subj');
                    $subject = [
                        'subjectCode' => $subj->subjectCode,
                        'subjectName' => $subj->subjectName
                    ];
                    /**
                     * Fires after subject has been created.
                     *
                     * @since 6.1.07
                     * @param array $subject
                     *            Subject data object.
                     */
                    do_action('post_save_subject', $subject);
                    
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Subject', _filter_input_string(INPUT_POST, 'subjectName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'subjectCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/subject' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $subj = $app->db->subject()
                ->whereNot('subjectCode', 'NULL')
                ->orderBy('subjectCode');
            
            $q = etsis_cache_get('subj', 'subj');
            if (empty($q)) {
                $q = $subj->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('subj', $q, 'subj');
            }
            
            $app->view->display('form/subject', [
                'title' => 'Subject',
                'cssArray' => $css,
                'jsArray' => $js,
                'subj' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/subject/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/subject/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $subj = $app->db->subject();
                foreach ($_POST as $k => $v) {
                    $subj->$k = $v;
                }
                $subj->where('subjectID = ?', $id);
                if ($subj->update()) {
                    etsis_cache_flush_namespace('subj');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Subject', _filter_input_string(INPUT_POST, 'subjectName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'subjectCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $subj = $app->db->subject()
                ->where('subjectID = ?', $id);
            
            $q = etsis_cache_get($id, 'subj');
            if (empty($q)) {
                $q = $subj->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'subj');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['subjectID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-subject', [
                    'title' => 'View Subject',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'subj' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/student-load-rule/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/student-load-rule/', function () use($app, $css, $js, $flashNow) {
            if ($app->req->isPost()) {
                $slr = $app->db->student_load_rule();
                foreach ($_POST as $k => $v) {
                    $slr->$k = $v;
                }
                if ($slr->save()) {
                    $ID = $slr->lastInsertId();
                    etsis_cache_flush_namespace('slr');
                    $app->flash('success_message', $flashNow->notice(200));
                    redirect(get_base_url() . 'form/student-load-rule' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $slr = $app->db->student_load_rule()
                ->orderBy('min_cred', 'DESC');
            
            $q = etsis_cache_get('slr', 'slr');
            if (empty($q)) {
                $q = $slr->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('slr', $q, 'slr');
            }
            
            $app->view->display('form/student-load-rule', [
                'title' => 'Student Load Rule',
                'cssArray' => $css,
                'jsArray' => $js,
                'slr' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/student-load-rule/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/student-load-rule/(\d+)/', function ($id) use($app, $css, $js, $flashNow) {
            if ($app->req->isPost()) {
                $slr = $app->db->student_load_rule();
                foreach ($_POST as $k => $v) {
                    $slr->$k = $v;
                }
                $slr->where('slrID = ?', $id);
                if ($slr->update()) {
                    etsis_cache_flush_namespace('slr');
                    $app->flash('success_message', $flashNow->notice(200));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $slr = $app->db->student_load_rule()
                ->where('slrID = ?', $id);
            
            $q = etsis_cache_get($id, 'slr');
            if (empty($q)) {
                $q = $slr->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($$id, $q, 'slr');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['slrID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-student-load-rule', [
                    'title' => 'View Student Load Rule',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'slr' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/degree/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/degree/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $degree = $app->db->degree();
                foreach ($_POST as $k => $v) {
                    $degree->$k = $v;
                }
                if ($degree->save()) {
                    $ID = $degree->lastInsertId();
                    etsis_cache_flush_namespace('deg');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Degree', _filter_input_string(INPUT_POST, 'degreeName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'degreeCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/degree' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $degree = $app->db->degree()
                ->whereNot('degreeCode', 'NULL')
                ->orderBy('degreeCode');
            
            $q = etsis_cache_get('deg', 'deg');
            if (empty($q)) {
                $q = $degree->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('deg', $q, 'deg');
            }
            
            $app->view->display('form/degree', [
                'title' => 'Degree',
                'cssArray' => $css,
                'jsArray' => $js,
                'degree' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/degree/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/degree/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $degree = $app->db->degree();
                foreach ($_POST as $k => $v) {
                    $degree->$k = $v;
                }
                $degree->where('degreeID = ?', $id);
                if ($degree->update()) {
                    etsis_cache_flush_namespace('deg');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Degree', _filter_input_string(INPUT_POST, 'degreeName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'degreeCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $degree = $app->db->degree()
                ->where('degreeID = ?', $id);
            
            $q = etsis_cache_get($id, 'deg');
            if (empty($q)) {
                $q = $degree->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'deg');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['degreeID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-degree', [
                    'title' => 'View Degree',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'degree' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/major/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/major/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $major = $app->db->major();
                foreach ($_POST as $k => $v) {
                    $major->$k = $v;
                }
                if ($major->save()) {
                    $ID = $major->lastInsertId();
                    etsis_cache_flush_namespace('majr');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Major', _filter_input_string(INPUT_POST, 'majorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'majorCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/major' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $major = $app->db->major()
                ->whereNot('majorCode', 'NULL')
                ->orderBy('majorCode');
            
            $q = etsis_cache_get('majr', 'majr');
            if (empty($q)) {
                $q = $major->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('majr', $q, 'majr');
            }
            
            $app->view->display('form/major', [
                'title' => 'Major',
                'cssArray' => $css,
                'jsArray' => $js,
                'major' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/major/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/major/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $major = $app->db->major();
                foreach ($_POST as $k => $v) {
                    $major->$k = $v;
                }
                $major->where('majorID = ?', $id);
                if ($major->update()) {
                    etsis_cache_flush_namespace('majr');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Major', _filter_input_string(INPUT_POST, 'majorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'majorCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $major = $app->db->major()
                ->where('majorID = ?', $id);
            
            $q = etsis_cache_get($id, 'majr');
            if (empty($q)) {
                $q = $major->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'majr');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['majorID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-major', [
                    'title' => 'View Major',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'major' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/minor/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/minor/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $minor = $app->db->minor();
                foreach ($_POST as $k => $v) {
                    $minor->$k = $v;
                }
                if ($minor->save()) {
                    $ID = $minor->lastInsertId();
                    etsis_cache_flush_namespace('minr');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Minor', _filter_input_string(INPUT_POST, 'minorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'minorCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/minor' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $minor = $app->db->minor()
                ->whereNot('minorCode', 'NULL')
                ->orderBy('minorCode');
            
            $q = etsis_cache_get('minr', 'minr');
            if (empty($q)) {
                $q = $minor->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('minr', $q, 'minr');
            }
            
            $app->view->display('form/minor', [
                'title' => 'Minor',
                'cssArray' => $css,
                'jsArray' => $js,
                'minor' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/minor/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/minor/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $minor = $app->db->minor();
                foreach ($_POST as $k => $v) {
                    $minor->$k = $v;
                }
                $minor->where('minorID = ?', $id);
                if ($minor->update()) {
                    etsis_cache_flush_namespace('minr');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Minor', _filter_input_string(INPUT_POST, 'minorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'minorCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $minor = $app->db->minor()
                ->where('minorID = ?', $id);
            
            $q = etsis_cache_get($id, 'minr');
            if (empty($q)) {
                $q = $minor->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'minr');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['minorID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-minor', [
                    'title' => 'View Minor',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'minor' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/ccd/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/ccd/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $ccd = $app->db->ccd();
                foreach ($_POST as $k => $v) {
                    $ccd->$k = $v;
                }
                if ($ccd->save()) {
                    $ID = $ccd->lastInsertId();
                    etsis_cache_flush_namespace('ccd');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'CCD', _filter_input_string(INPUT_POST, 'ccdName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'ccdCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/ccd' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            $ccd = $app->db->ccd()
                ->whereNot('ccdCode', 'NULL')
                ->orderBy('ccdCode');
            
            $q = etsis_cache_get('ccd', 'ccd');
            if (empty($q)) {
                $q = $ccd->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('ccd', $q, 'ccd');
            }
            
            $app->view->display('form/ccd', [
                'title' => 'CCD',
                'cssArray' => $css,
                'jsArray' => $js,
                'ccd' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/ccd/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/ccd/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $ccd = $app->db->ccd();
                foreach ($_POST as $k => $v) {
                    $ccd->$k = $v;
                }
                $ccd->where('ccdID = ?', $id);
                if ($ccd->update()) {
                    etsis_cache_flush_namespace('ccd');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'CCD', _filter_input_string(INPUT_POST, 'ccdName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'ccdCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $ccd = $app->db->ccd()
                ->where('ccdID = ?', $id);
            
            $q = etsis_cache_get($id, 'ccd');
            if (empty($q)) {
                $q = $ccd->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'ccd');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['ccdID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-ccd', [
                    'title' => 'View CCD',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'ccd' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/specialization/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/specialization/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $spec = $app->db->specialization();
                foreach ($_POST as $k => $v) {
                    $spec->$k = $v;
                }
                if ($spec->save()) {
                    $ID = $spec->lastInsertId();
                    etsis_cache_flush_namespace('spec');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Specialization', _filter_input_string(INPUT_POST, 'specName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'specCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/specialization' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $spec = $app->db->specialization()
                ->whereNot('specCode', 'NULL')
                ->orderBy('specCode');
            
            $q = etsis_cache_get('spec', 'spec');
            if (empty($q)) {
                $q = $spec->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('spec', $q, 'spec');
            }
            
            $app->view->display('form/specialization', [
                'title' => 'Specialization',
                'cssArray' => $css,
                'jsArray' => $js,
                'spec' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/specialization/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/specialization/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $spec = $app->db->specialization();
                foreach ($_POST as $k => $v) {
                    $spec->$k = $v;
                }
                $spec->where('specID = ?', $id);
                if ($spec->update()) {
                    etsis_cache_flush_namespace('spec');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Specialization', _filter_input_string(INPUT_POST, 'specName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'specCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $spec = $app->db->specialization()
                ->where('specID = ?', $id);
            
            $q = etsis_cache_get($id, 'spec');
            if (empty($q)) {
                $q = $spec->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'spec');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['specID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-specialization', [
                    'title' => 'View Specialization',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'spec' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/cip/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/cip/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $cip = $app->db->cip();
                foreach ($_POST as $k => $v) {
                    $cip->$k = $v;
                }
                if ($cip->save()) {
                    $ID = $cip->lastInsertId();
                    etsis_cache_flush_namespace('cip');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'CIP', _filter_input_string(INPUT_POST, 'cipName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'cipCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/cip' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $cip = $app->db->cip()
                ->whereNot('cipCode', 'NULL')
                ->orderBy('cipCode');
            
            $q = etsis_cache_get('cip', 'cip');
            if (empty($q)) {
                $q = $cip->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('cip', $q, 'cip');
            }
            
            $app->view->display('form/cip', [
                'title' => 'CIP',
                'cssArray' => $css,
                'jsArray' => $js,
                'cip' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/cip/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/cip/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $cip = $app->db->cip();
                foreach ($_POST as $k => $v) {
                    $cip->$k = $v;
                }
                $cip->where('cipID = ?', $id);
                if ($cip->update()) {
                    etsis_cache_flush_namespace('cip');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'CIP', _filter_input_string(INPUT_POST, 'cipName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'cipCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $cip = $app->db->cip()
                ->where('cipID = ?', $id);
            
            $q = etsis_cache_get($id, 'cip');
            if (empty($q)) {
                $q = $cip->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'cip');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['cipID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-cip', [
                    'title' => 'View CIP',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'cip' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/rstr-code/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/rstr-code/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $rstr = $app->db->restriction_code();
                foreach ($_POST as $k => $v) {
                    $rstr->$k = $v;
                }
                if ($rstr->save()) {
                    $ID = $rstr->lastInsertId();
                    etsis_cache_flush_namespace('rstr');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Restriction Code', _filter_input_string(INPUT_POST, 'rstrCode'), get_persondata('uname'));
                    redirect(get_base_url() . 'form/rstr-code' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $rstr = $app->db->restriction_code()
                ->select('restriction_code.*,department.deptName')
                ->_join('department', 'restriction_code.deptCode = department.deptCode')
                ->orderBy('rstrCode');
            
            $q = etsis_cache_get('rstr', 'rstr');
            if (empty($q)) {
                $q = $rstr->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('rstr', $q, 'rstr');
            }
            
            $app->view->display('form/rstr-code', [
                'title' => 'Restriction Code',
                'cssArray' => $css,
                'jsArray' => $js,
                'rstr' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/rstr-code/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/rstr-code/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $rstr = $app->db->restriction_code();
                foreach ($_POST as $k => $v) {
                    $rstr->$k = $v;
                }
                $rstr->where('rstrCodeID = ?', $id);
                if ($rstr->update()) {
                    etsis_cache_flush_namespace('rstr');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Restriction Code', _filter_input_string(INPUT_POST, 'rstrCode'), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $rstr = $app->db->restriction_code()
                ->where('rstrCodeID = ?', $id);
            
            $q = etsis_cache_get($id, 'rstr');
            if (empty($q)) {
                $q = $rstr->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'rstr');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['rstrCodeID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-rstr-code', [
                    'title' => 'View Restriction Code',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'rstr' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/location/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/location/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $loc = $app->db->location();
                foreach ($_POST as $k => $v) {
                    $loc->$k = $v;
                }
                if ($loc->save()) {
                    $ID = $loc->lastInsertId();
                    etsis_cache_flush_namespace('loc');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Location', _filter_input_string(INPUT_POST, 'locationCode') . ' (' . _trim(_filter_input_string(INPUT_POST, 'locationCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/location' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $location = $app->db->location()
                ->whereNot('locationCode', 'NULL')
                ->orderBy('locationCode');
            
            $q = etsis_cache_get('loc', 'loc');
            if (empty($q)) {
                $q = $location->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('loc', $q, 'loc');
            }
            
            $app->view->display('form/location', [
                'title' => 'Location',
                'cssArray' => $css,
                'jsArray' => $js,
                'location' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/location/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/location/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $loc = $app->db->location();
                foreach ($_POST as $k => $v) {
                    $loc->$k = $v;
                }
                $loc->where('locationID = ?', $id);
                if ($loc->update()) {
                    etsis_cache_flush_namespace('loc');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Location Code', _filter_input_string(INPUT_POST, 'locationCode') . ' (' . _trim(_filter_input_string(INPUT_POST, 'locationCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $loc = $app->db->location()
                ->where('locationID = ?', $id);
            
            $q = etsis_cache_get($id, 'loc');
            if (empty($q)) {
                $q = $loc->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'loc');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['locationID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-location', [
                    'title' => 'View Location',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'location' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/building/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/building/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $build = $app->db->building();
                foreach ($_POST as $k => $v) {
                    $build->$k = $v;
                }
                if ($build->save()) {
                    $ID = $build->lastInsertId();
                    etsis_cache_flush_namespace('bldg');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Building', _filter_input_string(INPUT_POST, 'buildingName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'buildingCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/building' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $build = $app->db->building()
                ->whereNot('buildingCode', 'NULL')
                ->orderBy('buildingCode');
            
            $q = etsis_cache_get('bldg', 'bldg');
            if (empty($q)) {
                $q = $build->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('bldg', $q, 'bldg');
            }
            
            $app->view->display('form/building', [
                'title' => 'Building',
                'cssArray' => $css,
                'jsArray' => $js,
                'build' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/building/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/building/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $build = $app->db->building();
                foreach ($_POST as $k => $v) {
                    $build->$k = $v;
                }
                $build->where('buildingID = ?', $id);
                if ($build->update()) {
                    etsis_cache_flush_namespace('bldg');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Building', _filter_input_string(INPUT_POST, 'buildingName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'buildingCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $build = $app->db->building()
                ->where('buildingID = ?', $id);
            
            $q = etsis_cache_get($id, 'bldg');
            if (empty($q)) {
                $q = $build->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'bldg');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['buildingID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-building', [
                    'title' => 'View Building',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'build' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/room/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/room/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $room = $app->db->room();
                foreach ($_POST as $k => $v) {
                    $room->$k = $v;
                }
                if ($room->save()) {
                    $ID = $room->lastInsertId();
                    etsis_cache_flush_namespace('room');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Room', _filter_input_string(INPUT_POST, 'roomCode'), get_persondata('uname'));
                    redirect(get_base_url() . 'form/room' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $room = $app->db->room()
                ->select('room.*,building.buildingName')
                ->_join('building', 'room.buildingCode = building.buildingCode')
                ->where('roomCode <> "NULL"')
                ->orderBy('buildingName');
            
            $q = etsis_cache_get('room', 'room');
            if (empty($q)) {
                $q = $room->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('room', $q, 'room');
            }
            
            $app->view->display('form/room', [
                'title' => 'Room',
                'cssArray' => $css,
                'jsArray' => $js,
                'room' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/room/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/room/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $room = $app->db->room();
                foreach ($_POST as $k => $v) {
                    $room->$k = $v;
                }
                $room->where('roomID = ?', $id);
                if ($room->update()) {
                    etsis_cache_flush_namespace('room');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Room', _filter_input_string(INPUT_POST, 'roomCode'), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $room = $app->db->room()
                ->where('roomID = ?', $id);
            
            $q = etsis_cache_get($id, 'room');
            if (empty($q)) {
                $q = $room->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'room');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['roomID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-room', [
                    'title' => 'View Room',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'room' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/school/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/school/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $school = $app->db->school();
                foreach ($_POST as $k => $v) {
                    $school->$k = $v;
                }
                if ($school->save()) {
                    $ID = $school->lastInsertId();
                    etsis_cache_flush_namespace('sch');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'School', _filter_input_string(INPUT_POST, 'schoolName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'schoolCode')) . ')', get_persondata('uname'));
                    redirect(get_base_url() . 'form/school' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $sch = $app->db->school()
                ->select('school.*,building.buildingName')
                ->_join('building', 'school.buildingCode = building.buildingCode')
                ->where('schoolCode <> "NULL"')
                ->orderBy('buildingName');
            
            $q = etsis_cache_get('sch', 'sch');
            if (empty($q)) {
                $q = $sch->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('sch', $q, 'sch');
            }
            
            $app->view->display('form/school', [
                'title' => 'School',
                'cssArray' => $css,
                'jsArray' => $js,
                'school' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/school/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/school/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $school = $app->db->school();
                foreach ($_POST as $k => $v) {
                    $school->$k = $v;
                }
                $school->where('schoolID = ?', $id);
                if ($school->update()) {
                    etsis_cache_flush_namespace('sch');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'School', _filter_input_string(INPUT_POST, 'schoolName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'schoolCode')) . ')', get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $school = $app->db->school()
                ->where('schoolID = ?', $id);
            
            $q = etsis_cache_get($id, 'sch');
            if (empty($q)) {
                $q = $school->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'sch');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['schoolID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-school', [
                    'title' => 'View School',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'school' => $q
                ]);
            }
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/grade-scale/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/grade-scale/', function () use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $gs = $app->db->grade_scale();
                foreach ($_POST as $k => $v) {
                    $gs->$k = $v;
                }
                if ($gs->save()) {
                    $ID = $gs->lastInsertId();
                    etsis_cache_flush_namespace('grsc');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('New Record', 'Grade Scale', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
                    redirect(get_base_url() . 'form/grade-scale' . DS . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            }
            
            $scale = $app->db->grade_scale()
                ->orderBy('grade');
            
            $q = etsis_cache_get('grsc', 'grsc');
            if (empty($q)) {
                $q = $scale->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('grsc', $q, 'grsc');
            }
            
            $app->view->display('form/grade-scale', [
                'title' => 'Grade Scale',
                'cssArray' => $css,
                'jsArray' => $js,
                'scale' => $q
            ]);
        });
        
        /**
         * Before route check.
         */
        $app->before('GET|POST', '/grade-scale/(\d+)/', function () {
            if (! hasPermission('access_forms')) {
                redirect(get_base_url() . 'dashboard' . DS);
            }
            
            /**
             * If user is logged in and the lockscreen cookie is set,
             * redirect user to the lock screen until he/she enters
             * his/her password to gain access.
             */
            if (isset($_COOKIE['SCREENLOCK'])) {
                redirect(get_base_url() . 'lock' . DS);
            }
        });
        
        $app->match('GET|POST', '/grade-scale/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
            if ($app->req->isPost()) {
                $gs = $app->db->grade_scale();
                foreach ($_POST as $k => $v) {
                    $gs->$k = $v;
                }
                $gs->where('ID = ?', $id);
                if ($gs->update()) {
                    etsis_cache_flush_namespace('grsc');
                    $app->flash('success_message', $flashNow->notice(200));
                    $logger->setLog('Update Record', 'Grade Scale', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                }
                redirect($app->req->server['HTTP_REFERER']);
            }
            
            $gs = $app->db->grade_scale()
                ->where('ID = ?', $id);
            
            $q = etsis_cache_get($id, 'grsc');
            if (empty($q)) {
                $q = $gs->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'grsc');
            }
            
            /**
             * If the database table doesn't exist, then it
             * is false and a 404 should be sent.
             */
            if ($q == false) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If the query is legit, but there
             * is no data in the table, then 404
             * will be shown.
             */
            elseif (empty($q) == true) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If data is zero, 404 not found.
             */
            elseif (count($q[0]['ID']) <= 0) {
                
                $app->view->display('error/404', [
                    'title' => '404 Error'
                ]);
            } /**
             * If we get to this point, the all is well
             * and it is ok to process the query and print
             * the results in a html format.
             */
            else {
                
                $app->view->display('form/view-grade-scale', [
                    'title' => 'View Grade Scale',
                    'cssArray' => $css,
                    'jsArray' => $js,
                    'scale' => $q
                ]);
            }
        });
        
        $app->setError(function () use($app) {
            
            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        });
    });

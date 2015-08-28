<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Form Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$logger = new \app\src\Log();
$flashNow = new \app\src\Messages();

$css = [ 'css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
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

$app->group('/form', function() use ($app, $css, $js, $logger, $dbcache, $flashNow) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/semester/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/semester/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$sem = $app->db->semester();
    		foreach($_POST as $k => $v) {
    			$sem->$k = $v;
    		}
    		if($sem->save()) {
    			$ID = $sem->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Semester', _filter_input_string(INPUT_POST, 'semName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'semCode')) . ')', get_persondata('uname'));
                redirect( url('/form/semester/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect($app->req->server['HTTP_REFERER']);
    		}
    	}
        
        $sem = $app->db->semester()->whereNot('semCode', 'NULL')->orderBy('acadYearCode', 'DESC');
        $q = $sem->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/semester', [
            'title' => 'Semester',
            'cssArray' => $css,
            'jsArray' => $js,
            'semester' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/semester/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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
    $app->match('GET|POST', '/semester/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$sem = $app->db->semester();
    		foreach($_POST as $k => $v) {
    			$sem->$k = $v;
    		}
    		$sem->where('semesterID = ?', $id);
    		if($sem->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Semester', _filter_input_string(INPUT_POST, 'semName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'semCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
        $sem = $app->db->semester()->where('semesterID = ?', $id);
        $q = $sem->find(function($data) {
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
         */ elseif (count($q[0]['semesterID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-semester', [
                'title' => 'View Semester',
                'cssArray' => $css,
                'jsArray' => $js,
                'semester' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/term/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/term/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$term = $app->db->term();
    		foreach($_POST as $k => $v) {
    			$term->$k = $v;
    		}
    		if($term->save()) {
    			$ID = $term->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Term', _filter_input_string(INPUT_POST, 'termName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'termCode')) . ')', get_persondata('uname'));
                redirect( url('/form/term/') . $ID . '/' );
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
        $q = $term->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/term', [
            'title' => 'Term',
            'cssArray' => $css,
            'jsArray' => $js,
            'term' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/term/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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
    $app->match('GET|POST', '/term/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$term = $app->db->term();
    		foreach($_POST as $k => $v) {
    			$term->$k = $v;
    		}
    		$term->where('termID = ?', $id);
    		if($term->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Term', _filter_input_string(INPUT_POST, 'termName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'termCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
        $term = $app->db->term()->where('termID = ?', $id);
        $q = $term->find(function($data) {
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
         */ elseif (count($q[0]['termID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-term', [
                'title' => 'View Term',
                'cssArray' => $css,
                'jsArray' => $js,
                'term' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/acad-year/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/acad-year/', function () use($app, $css, $js, $logger, $flashNow) {
    	if($app->req->isPost()) {
    		$year = $app->db->acad_year();
    		foreach($_POST as $k => $v) {
    			$year->$k = $v;
    		}
    		if($year->save()) {
    			$ID = $year->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Academic Year', _filter_input_string(INPUT_POST,'acadYearDesc'), get_persondata('uname'));
                redirect( url('/form/acad-year/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
                redirect( $app->req->server['HTTP_REFERER'] );
    		}
    	}
        $ay = $app->db->acad_year()->whereNot('acadYearCode', 'NULL')->orderBy('acadYearCode', 'DESC');
        $q = $ay->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/acad-year', [
            'title' => 'Academic Year',
            'cssArray' => $css,
            'jsArray' => $js,
            'acadYear' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/acad-year/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/acad-year/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
    	if($app->req->isPost()) {
    		$year = $app->db->acad_year();
    		foreach($_POST as $k => $v) {
    			$year->$k = $v;
    		}
    		$year->where('acadYearID = ?', $id);
    		if($year->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Academic Year', _filter_input_string(INPUT_POST, 'acadYearDesc'), get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
        $ay = $app->db->acad_year()->where('acadYearID = ?', $id);
        $q = $ay->find(function($data) {
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
         */ elseif (count($q[0]['acadYearID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-acad-year', [
                'title' => 'View Acad Year',
                'cssArray' => $css,
                'jsArray' => $js,
                'acadYear' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/department/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/department/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$dept = $app->db->department();
    		foreach($_POST as $k => $v) {
    			$dept->$k = $v;
    		}
    		if($dept->save()) {
    			$ID = $dept->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Department', _filter_input_string(INPUT_POST, 'deptName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'deptCode')) . ')', get_persondata('uname'));
                redirect( url('/form/department/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect( $app->req->server['HTTP_REFERER'] );
    		}
    	}
        
        $dept = $app->db->department()->whereNot('deptCode', 'NULL')->orderBy('deptCode');
        $q = $dept->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/department', [
            'title' => 'Department',
            'cssArray' => $css,
            'jsArray' => $js,
            'dept' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/department/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/department/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$dept = $app->db->department();
    		foreach($_POST as $k => $v) {
    			$dept->$k = $v;
    		}
    		$dept->where('deptID = ?', $id);
    		if($dept->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Department', _filter_input_string(INPUT_POST, 'deptName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'deptCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
        $dept = $app->db->department()->where('deptID = ?', $id);
        $q = $dept->find(function($data) {
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
         */ elseif (count($q[0]['deptID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-department', [
                'title' => 'View Department',
                'cssArray' => $css,
                'jsArray' => $js,
                'dept' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/subject/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/subject/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
            $subj = $app->db->subject();
            $subj->subjectCode = _filter_input_string(INPUT_POST, 'subjectCode');
            $subj->subjectName = _filter_input_string(INPUT_POST, 'subjectName');
            if($subj->save()) {
                $ID = $subj->lastInsertId();
                /**
                 * Create Subject Action Hook
                 * 
                 * Fired when a subject code is created.
                 * 
                 * @return mixed
                 */
                do_action( 'create_subject_code', _trim(_filter_input_string(INPUT_POST, 'subjectCode')), _filter_input_string(INPUT_POST, 'subjectName') );
                $app->flash( 'success_message', $flashNow->notice(200) );
                $logger->setLog('New Record', 'Subject', _filter_input_string(INPUT_POST, 'subjectName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'subjectCode')) . ')', get_persondata('uname'));
                redirect( url('/form/subject/') . $ID . '/' );
            } else {
                $app->flash( 'error_message', $flashNow->notice(409) );
                redirect($app->req->server['HTTP_REFERER']);
            }
        }
        $subj = $app->db->subject()->whereNot('subjectCode', 'NULL')->orderBy('subjectCode');
        $q = $subj->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/subject', [
            'title' => 'Subject',
            'cssArray' => $css,
            'jsArray' => $js,
            'subj' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/subject/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/subject/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$subj = $app->db->subject();
    		foreach($_POST as $k => $v) {
    			$subj->$k = $v;
    		}
    		$subj->where('subjectID = ?', $id);
    		if($subj->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Subject', _filter_input_string(INPUT_POST,'subjectName') . ' (' . _trim(_filter_input_string(INPUT_POST,'subjectCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
        $subj = $app->db->subject()->where('subjectID = ?', $id);
        $q = $subj->find(function($data) {
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
         */ elseif (count($q[0]['subjectID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-subject', [
                'title' => 'View Subject',
                'cssArray' => $css,
                'jsArray' => $js,
                'subj' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/student-load-rule/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/student-load-rule/', function () use($app, $css, $js, $flashNow) {
        if($app->req->isPost()) {
    		$slr = $app->db->student_load_rule();
    		foreach($_POST as $k => $v) {
    			$slr->$k = $v;
    		}
    		if($slr->save()) {
    			$ID = $slr->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                redirect( url('/form/student-load-rule/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect( $app->req->server['HTTP_REFERER'] );
    		}
    	}
        
        $slr = $app->db->student_load_rule()->orderBy('min_cred', 'DESC');
        $q = $slr->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/student-load-rule', [
            'title' => 'Student Load Rule',
            'cssArray' => $css,
            'jsArray' => $js,
            'slr' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/student-load-rule/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/student-load-rule/(\d+)/', function ($id) use($app, $css, $js, $flashNow) {
        if($app->req->isPost()) {
    		$slr = $app->db->student_load_rule();
    		foreach($_POST as $k => $v) {
    			$slr->$k = $v;
    		}
    		$slr->where('slrID = ?', $id);
    		if($slr->update()) {
                $app->flash('success_message', $flashNow->notice(200));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
        $slr = $app->db->student_load_rule()->where('slrID = ?', $id);
        $q = $slr->find(function($data) {
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
         */ elseif (count($q[0]['slrID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-student-load-rule', [
                'title' => 'View Student Load Rule',
                'cssArray' => $css,
                'jsArray' => $js,
                'slr' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/degree/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/degree/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$degree = $app->db->degree();
    		foreach($_POST as $k => $v) {
    			$degree->$k = $v;
    		}
    		if($degree->save()) {
    			$ID = $degree->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Degree', _filter_input_string(INPUT_POST, 'degreeName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'degreeCode')) . ')', get_persondata('uname'));
                redirect( url('/form/degree/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect( $app->req->server['HTTP_REFERER'] );
    		}
    	}
        $degree = $app->db->degree()->whereNot('degreeCode', 'NULL')->orderBy('degreeCode');
        $q = $degree->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/degree', [
            'title' => 'Degree',
            'cssArray' => $css,
            'jsArray' => $js,
            'degree' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/degree/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/degree/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$degree = $app->db->degree();
    		foreach($_POST as $k => $v) {
    			$degree->$k = $v;
    		}
            $degree->where('degreeID = ?', $id);
    		if($degree->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Degree', _filter_input_string(INPUT_POST, 'degreeName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'degreeCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
            redirect( $app->req->server['HTTP_REFERER'] );
    	}
        
        $degree = $app->db->degree()->where('degreeID = ?', $id);
        $q = $degree->find(function($data) {
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
         */ elseif (count($q[0]['degreeID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-degree', [
                'title' => 'View Degree',
                'cssArray' => $css,
                'jsArray' => $js,
                'degree' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/major/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/major/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$major = $app->db->major();
    		foreach($_POST as $k => $v) {
    			$major->$k = $v;
    		}
    		if($major->save()) {
    			$ID = $major->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Major', _filter_input_string(INPUT_POST, 'majorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'majorCode')) . ')', get_persondata('uname'));
                redirect( url('/form/major/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect( $app->req->server['HTTP_REFERER'] );
    		}
    	}
        
        $major = $app->db->major()->whereNot('majorCode', 'NULL')->orderBy('majorCode');
        $q = $major->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/major', [
            'title' => 'Major',
            'cssArray' => $css,
            'jsArray' => $js,
            'major' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/major/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/major/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$major = $app->db->major();
    		foreach($_POST as $k => $v) {
    			$major->$k = $v;
    		}
            $major->where('majorID = ?', $id);
    		if($major->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Major', _filter_input_string(INPUT_POST, 'majorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'majorCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
            redirect( $app->req->server['HTTP_REFERER'] );
    	}
        
        $major = $app->db->major()->where('majorID = ?', $id);
        $q = $major->find(function($data) {
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
         */ elseif (count($q[0]['majorID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-major', [
                'title' => 'View Major',
                'cssArray' => $css,
                'jsArray' => $js,
                'major' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/minor/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/minor/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$minor = $app->db->minor();
    		foreach($_POST as $k => $v) {
    			$minor->$k = $v;
    		}
    		if($minor->save()) {
    			$ID = $minor->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Minor', _filter_input_string(INPUT_POST, 'minorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'minorCode')) . ')', get_persondata('uname'));
                redirect( url('/form/minor/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect( $app->req->server['HTTP_REFERER'] );
    		}
    	}
        
        $minor = $app->db->minor()->whereNot('minorCode', 'NULL')->orderBy('minorCode');
        $q = $minor->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/minor', [
            'title' => 'Minor',
            'cssArray' => $css,
            'jsArray' => $js,
            'minor' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/minor/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/minor/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$minor = $app->db->minor();
    		foreach($_POST as $k => $v) {
    			$minor->$k = $v;
    		}
            $minor->where('minorID = ?', $id);
    		if($minor->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Minor', _filter_input_string(INPUT_POST, 'minorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'minorCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
            redirect( $app->req->server['HTTP_REFERER'] );
    	}
        
        $minor = $app->db->minor()->where('minorID = ?', $id);
        $q = $minor->find(function($data) {
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
         */ elseif (count($q[0]['minorID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-minor', [
                'title' => 'View Minor',
                'cssArray' => $css,
                'jsArray' => $js,
                'minor' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/ccd/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/ccd/', function () use($app, $css, $js, $logger, $flashNow) {
    	if($app->req->isPost()) {
    		$ccd = $app->db->ccd();
    		foreach($_POST as $k => $v) {
    			$ccd->$k = $v;
    		}
    		if($ccd->save()) {
    			$ID = $ccd->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'CCD', _filter_input_string(INPUT_POST, 'ccdName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'ccdCode')) . ')', get_persondata('uname'));
                redirect( url('/form/ccd/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect( $app->req->server['HTTP_REFERER'] );
    		}
    	}
        $ccd = $app->db->ccd()->whereNot('ccdCode', 'NULL')->orderBy('ccdCode');
        $q = $ccd->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/ccd', [
            'title' => 'CCD',
            'cssArray' => $css,
            'jsArray' => $js,
            'ccd' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/ccd/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/ccd/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$ccd = $app->db->ccd();
    		foreach($_POST as $k => $v) {
    			$ccd->$k = $v;
    		}
    		$ccd->where('ccdID = ?', $id);
    		if($ccd->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'CCD', _filter_input_string(INPUT_POST, 'ccdName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'ccdCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
    	
    	$ccd = $app->db->ccd()->where('ccdID = ?', $id);
        $q = $ccd->find(function($data) {
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
         */ elseif (count($q[0]['ccdID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-ccd', [
                'title' => 'View CCD',
                'cssArray' => $css,
                'jsArray' => $js,
                'ccd' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/specialization/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/specialization/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$spec = $app->db->specialization();
    		foreach($_POST as $k => $v) {
    			$spec->$k = $v;
    		}
    		if($spec->save()) {
    			$ID = $spec->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Specialization', _filter_input_string(INPUT_POST, 'specName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'specCode')) . ')', get_persondata('uname'));
                redirect( url('/form/specialization/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect( $app->req->server['HTTP_REFERER'] );
    		}
    	}
        
        $spec = $app->db->specialization()->whereNot('specCode', 'NULL')->orderBy('specCode');
        $q = $spec->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/specialization', [
            'title' => 'Specialization',
            'cssArray' => $css,
            'jsArray' => $js,
            'spec' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/specialization/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/specialization/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$spec = $app->db->specialization();
    		foreach($_POST as $k => $v) {
    			$spec->$k = $v;
    		}
    		$spec->where('specID = ?', $id);
    		if($spec->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Specialization', _filter_input_string(INPUT_POST, 'specName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'specCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
    	
    	$spec = $app->db->specialization()->where('specID = ?', $id);
        $q = $spec->find(function($data) {
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
         */ elseif (count($q[0]['specID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-specialization', [
                'title' => 'View Specialization',
                'cssArray' => $css,
                'jsArray' => $js,
                'spec' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/cip/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/cip/', function () use($app, $css, $js, $logger, $flashNow) {
    	if($app->req->isPost()) {
    		$cip = $app->db->cip();
    		foreach($_POST as $k => $v) {
    			$cip->$k = $v;
    		}
    		if($cip->save()) {
    			$ID = $cip->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'CIP', _filter_input_string(INPUT_POST, 'cipName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'cipCode')) . ')', get_persondata('uname'));
                redirect( url('/form/cip/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect($app->req->server['HTTP_REFERER']);
    		}
    	}
    	
        $cip = $app->db->cip()->whereNot('cipCode', 'NULL')->orderBy('cipCode');
        $q = $cip->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/cip', [
            'title' => 'CIP',
            'cssArray' => $css,
            'jsArray' => $js,
            'cip' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/cip/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/cip/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$cip = $app->db->cip();
    		foreach($_POST as $k => $v) {
    			$cip->$k = $v;
    		}
    		$cip->where('cipID = ?', $id);
    		if($cip->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'CIP', _filter_input_string(INPUT_POST, 'cipName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'cipCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
    	
    	$cip = $app->db->cip()->where('cipID = ?', $id);
        $q = $cip->find(function($data) {
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
         */ elseif (count($q[0]['cipID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-cip', [
                'title' => 'View CIP',
                'cssArray' => $css,
                'jsArray' => $js,
                'cip' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/rstr-code/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/rstr-code/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$rstr = $app->db->restriction_code();
    		foreach($_POST as $k => $v) {
    			$rstr->$k = $v;
    		}
    		if($rstr->save()) {
    			$ID = $rstr->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Restriction Code', _filter_input_string(INPUT_POST, 'rstrCode'), get_persondata('uname'));
                redirect( url('/form/rstr-code/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect($app->req->server['HTTP_REFERER']);
    		}
    	}
        
        $rstr = $app->db->restriction_code()
            ->select('restriction_code.*,department.deptName')
            ->_join('department', 'restriction_code.deptCode = department.deptCode')
            ->orderBy('rstrCode');
        $q = $rstr->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/rstr-code', [
            'title' => 'Restriction Code',
            'cssArray' => $css,
            'jsArray' => $js,
            'rstr' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/rstr-code/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/rstr-code/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$rstr = $app->db->restriction_code();
    		foreach($_POST as $k => $v) {
    			$rstr->$k = $v;
    		}
    		$rstr->where('rstrCodeID = ?', $id);
    		if($rstr->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Restriction Code', _filter_input_string(INPUT_POST, 'rstrCode'), get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
    	
    	$rstr = $app->db->restriction_code()->where('rstrCodeID = ?', $id);
        $q = $rstr->find(function($data) {
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
         */ elseif (count($q[0]['rstrCodeID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-rstr-code', [
                'title' => 'View Restriction Code',
                'cssArray' => $css,
                'jsArray' => $js,
                'rstr' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/location/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/location/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$loc = $app->db->location();
    		foreach($_POST as $k => $v) {
    			$loc->$k = $v;
    		}
    		if($loc->save()) {
    			$ID = $loc->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Location', _filter_input_string(INPUT_POST, 'locationCode') . ' (' . _trim(_filter_input_string(INPUT_POST, 'locationCode')) . ')', get_persondata('uname'));
                redirect( url('/form/location/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect($app->req->server['HTTP_REFERER']);
    		}
    	}
        
        $location = $app->db->location()->whereNot('locationCode', 'NULL')->orderBy('locationCode');
        $q = $location->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/location', [
            'title' => 'Location',
            'cssArray' => $css,
            'jsArray' => $js,
            'location' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/location/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/location/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$loc = $app->db->location();
    		foreach($_POST as $k => $v) {
    			$loc->$k = $v;
    		}
    		$loc->where('locationID = ?', $id);
    		if($loc->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Location Code', _filter_input_string(INPUT_POST, 'locationCode') . ' (' . _trim(_filter_input_string(INPUT_POST, 'locationCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
    	
    	$loc = $app->db->location()->where('locationID = ?', $id);
        $q = $loc->find(function($data) {
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
         */ elseif (count($q[0]['locationID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-location', [
                'title' => 'View Location',
                'cssArray' => $css,
                'jsArray' => $js,
                'location' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/building/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/building/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$build = $app->db->building();
    		foreach($_POST as $k => $v) {
    			$build->$k = $v;
    		}
    		if($build->save()) {
    			$ID = $build->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Building', _filter_input_string(INPUT_POST, 'buildingName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'buildingCode')) . ')', get_persondata('uname'));
                redirect( url('/form/building/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect($app->req->server['HTTP_REFERER']);
    		}
    	}
        
        $build = $app->db->building()->whereNot('buildingCode', 'NULL')->orderBy('buildingCode');
        $q = $build->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/building', [
            'title' => 'Building',
            'cssArray' => $css,
            'jsArray' => $js,
            'build' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/building/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/building/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$build = $app->db->building();
    		foreach($_POST as $k => $v) {
    			$build->$k = $v;
    		}
    		$build->where('buildingID = ?', $id);
    		if($build->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Building', _filter_input_string(INPUT_POST, 'buildingName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'buildingCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
    	
    	$build = $app->db->building()->where('buildingID = ?', $id);
        $q = $build->find(function($data) {
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
         */ elseif (count($q[0]['buildingID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-building', [
                'title' => 'View Building',
                'cssArray' => $css,
                'jsArray' => $js,
                'build' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/room/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/room/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$room = $app->db->room();
    		foreach($_POST as $k => $v) {
    			$room->$k = $v;
    		}
    		if($room->save()) {
    			$ID = $room->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Room', _filter_input_string(INPUT_POST, 'roomCode'), get_persondata('uname'));
                redirect( url('/form/room/') . $ID . '/' );
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
        $q = $room->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/room', [
            'title' => 'Room',
            'cssArray' => $css,
            'jsArray' => $js,
            'room' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/room/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/room/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$room = $app->db->room();
    		foreach($_POST as $k => $v) {
    			$room->$k = $v;
    		}
    		$room->where('roomID = ?', $id);
    		if($room->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Room', _filter_input_string(INPUT_POST, 'roomCode'), get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
    	
    	$room = $app->db->room()->where('roomID = ?', $id);
        $q = $room->find(function($data) {
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
         */ elseif (count($q[0]['roomID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-room', [
                'title' => 'View Room',
                'cssArray' => $css,
                'jsArray' => $js,
                'room' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/school/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/school/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$school = $app->db->school();
    		foreach($_POST as $k => $v) {
    			$school->$k = $v;
    		}
    		if($school->save()) {
    			$ID = $school->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'School', _filter_input_string(INPUT_POST, 'schoolName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'schoolCode')) . ')', get_persondata('uname'));
                redirect( url('/form/school/') . $ID . '/' );
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
        $q = $sch->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/school', [
            'title' => 'School',
            'cssArray' => $css,
            'jsArray' => $js,
            'school' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/school/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/school/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$school = $app->db->school();
    		foreach($_POST as $k => $v) {
    			$school->$k = $v;
    		}
    		$school->where('schoolID = ?', $id);
    		if($school->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'School', _filter_input_string(INPUT_POST, 'schoolName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'schoolCode')) . ')', get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
    	
    	$school = $app->db->school()->where('schoolID = ?', $id);
        $q = $school->find(function($data) {
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
         */ elseif (count($q[0]['schoolID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-school', [
                'title' => 'View School',
                'cssArray' => $css,
                'jsArray' => $js,
                'school' => $q
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/grade-scale/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/grade-scale/', function () use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$gs = $app->db->grade_scale();
    		foreach($_POST as $k => $v) {
    			$gs->$k = $v;
    		}
    		if($gs->save()) {
    			$ID = $gs->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Grade Scale', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
                redirect( url('/form/grade-scale/') . $ID . '/' );
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    			redirect($app->req->server['HTTP_REFERER']);
    		}
    	}
        
        $scale = $app->db->grade_scale()->orderBy('grade');
        $q = $scale->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/grade-scale', [
            'title' => 'Grade Scale',
            'cssArray' => $css,
            'jsArray' => $js,
            'scale' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/grade-scale/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
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

    $app->match('GET|POST', '/grade-scale/(\d+)/', function ($id) use($app, $css, $js, $logger, $flashNow) {
        if($app->req->isPost()) {
    		$gs = $app->db->grade_scale();
    		foreach($_POST as $k => $v) {
    			$gs->$k = $v;
    		}
    		$gs->where('ID = ?', $id);
    		if($gs->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update Record', 'Grade Scale', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
    		} else {
    			$app->flash('error_message', $flashNow->notice(409));
    		}
    		redirect( $app->req->server['HTTP_REFERER'] );
    	}
    	
    	$gs = $app->db->grade_scale()->where('ID = ?', $id);
        $q = $gs->find(function($data) {
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
         */ elseif (count($q[0]['ID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-grade-scale', [
                'title' => 'View Grade Scale',
                'cssArray' => $css,
                'jsArray' => $js,
                'scale' => $q
                ]
            );
        }
    });

    $app->setError(function() use($app) {

        $app->view->display('error/404', ['title' => '404 Error']);
    });
});

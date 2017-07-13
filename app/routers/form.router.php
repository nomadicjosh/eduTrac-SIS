<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use \app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

/**
 * Form Router
 *
 * @license GPLv3
 *         
 * @since 5.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
/**
 * Before router check.
 */
$app->before('GET|POST', '/form(.*)', function () {
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        exit();
    }

    if (!hasPermission('access_forms')) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        exit();
    }
});

$app->group('/form', function () use($app) {

    $app->match('GET|POST', '/semester/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $sem = $app->db->semester();
                foreach ($app->req->post as $k => $v) {
                    $sem->$k = $v;
                }
                $sem->save();

                $_id = $sem->lastInsertId();
                etsis_cache_flush_namespace('sem');
                etsis_logger_activity_log_write('New Record', 'Semester', _filter_input_string(INPUT_POST, 'semName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'semCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/semester' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datepicker');
        etsis_register_script('datatables');

        $app->view->display('form/semester', [
            'title' => 'Semester',
            'semester' => $q
        ]);
    });

    $app->match('GET|POST', '/semester/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $sem = $app->db->semester();
                foreach ($app->req->post as $k => $v) {
                    $sem->$k = $v;
                }
                $sem->where('id = ?', $id);
                $sem->update();

                etsis_cache_flush_namespace('sem');
                etsis_logger_activity_log_write('Update Record', 'Semester', _filter_input_string(INPUT_POST, 'semName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'semCode')) . ')', get_persondata('uname'));
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
            $sem = $app->db->semester()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count(_h($q[0]['id'])) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datepicker');

            $app->view->display('form/view-semester', [
                'title' => 'View Semester',
                'semester' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/term/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $term = $app->db->term();
                foreach ($app->req->post as $k => $v) {
                    $term->$k = $v;
                }
                $term->save();

                $_id = $term->lastInsertId();
                etsis_cache_flush_namespace('term');
                etsis_logger_activity_log_write('New Record', 'Term', _filter_input_string(INPUT_POST, 'termName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'termCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/term' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datepicker');
        etsis_register_script('datatables');

        $app->view->display('form/term', [
            'title' => 'Term',
            'term' => $q
        ]);
    });

    $app->match('GET|POST', '/term/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $term = $app->db->term();
                foreach ($app->req->post as $k => $v) {
                    $term->$k = $v;
                }
                $term->where('id = ?', $id);
                $term->update();

                etsis_cache_flush_namespace('term');
                etsis_logger_activity_log_write('Update Record', 'Term', _filter_input_string(INPUT_POST, 'termName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'termCode')) . ')', get_persondata('uname'));
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
            $term = $app->db->term()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datepicker');

            $app->view->display('form/view-term', [
                'title' => 'View Term',
                'term' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/acad-year/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $year = $app->db->acad_year();
                foreach ($app->req->post as $k => $v) {
                    $year->$k = $v;
                }
                $year->save();

                $_id = $year->lastInsertId();
                etsis_cache_flush_namespace('ayr');
                etsis_logger_activity_log_write('New Record', 'Academic Year', _filter_input_string(INPUT_POST, 'acadYearDesc'), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/acad-year' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/acad-year', [
            'title' => 'Academic Year',
            'acadYear' => $q
        ]);
    });

    $app->match('GET|POST', '/acad-year/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $year = $app->db->acad_year();
                foreach ($app->req->post as $k => $v) {
                    $year->$k = $v;
                }
                $year->where('id = ?', $id);
                $year->update();

                etsis_cache_flush_namespace('ayr');
                etsis_logger_activity_log_write('Update Record', 'Academic Year', _filter_input_string(INPUT_POST, 'acadYearDesc'), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                etsis_redirect($app->req->server['HTTP_REFERER']);
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
            $ay = $app->db->acad_year()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-acad-year', [
                'title' => 'View Acad Year',
                'acadYear' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/department/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $dept = $app->db->department();
                foreach ($app->req->post as $k => $v) {
                    $dept->$k = $v;
                }
                $dept->save();

                $_id = $dept->lastInsertId();
                etsis_cache_flush_namespace('dept');
                etsis_logger_activity_log_write('New Record', 'Department', _filter_input_string(INPUT_POST, 'deptName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'deptCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/department' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/department', [
            'title' => 'Department',
            'dept' => $q
        ]);
    });

    $app->match('GET|POST', '/department/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $dept = $app->db->department();
                foreach ($app->req->post as $k => $v) {
                    $dept->$k = $v;
                }
                $dept->where('id = ?', $id);
                $dept->update();

                etsis_cache_flush_namespace('dept');
                etsis_logger_activity_log_write('Update Record', 'Department', _filter_input_string(INPUT_POST, 'deptName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'deptCode')) . ')', get_persondata('uname'));
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
            $dept = $app->db->department()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-department', [
                'title' => 'View Department',
                'dept' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/subject/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $subj = $app->db->subject();
                $subj->subjectCode = _filter_input_string(INPUT_POST, 'subjectCode');
                $subj->subjectName = _filter_input_string(INPUT_POST, 'subjectName');
                $subj->save();

                $_id = $subj->lastInsertId();
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
                $app->hook->do_action('post_save_subject', $subject);

                etsis_logger_activity_log_write('New Record', 'Subject', _filter_input_string(INPUT_POST, 'subjectName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'subjectCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/subject' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/subject', [
            'title' => 'Subject',
            'subj' => $q
        ]);
    });

    $app->match('GET|POST', '/subject/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $subj = $app->db->subject();
                foreach ($app->req->post as $k => $v) {
                    $subj->$k = $v;
                }
                $subj->where('id = ?', $id);
                $subj->update();

                etsis_cache_flush_namespace('subj');
                etsis_logger_activity_log_write('Update Record', 'Subject', _filter_input_string(INPUT_POST, 'subjectName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'subjectCode')) . ')', get_persondata('uname'));
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
            $subj = $app->db->subject()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count(_h($q[0]['id'])) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-subject', [
                'title' => 'View Subject',
                'subj' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/degree/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $degree = $app->db->degree();
                foreach ($app->req->post as $k => $v) {
                    $degree->$k = $v;
                }
                $degree->save();

                $_id = $degree->lastInsertId();
                etsis_cache_flush_namespace('deg');
                etsis_logger_activity_log_write('New Record', 'Degree', _filter_input_string(INPUT_POST, 'degreeName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'degreeCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/degree' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/degree', [
            'title' => 'Degree',
            'degree' => $q
        ]);
    });

    $app->match('GET|POST', '/degree/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $degree = $app->db->degree();
                foreach ($app->req->post as $k => $v) {
                    $degree->$k = $v;
                }
                $degree->where('id = ?', $id);
                $degree->update();

                etsis_cache_flush_namespace('deg');
                etsis_logger_activity_log_write('Update Record', 'Degree', _filter_input_string(INPUT_POST, 'degreeName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'degreeCode')) . ')', get_persondata('uname'));
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
            $degree = $app->db->degree()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-degree', [
                'title' => 'View Degree',
                'degree' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/major/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $major = $app->db->major();
                foreach ($app->req->post as $k => $v) {
                    $major->$k = $v;
                }
                $major->save();

                $_id = $major->lastInsertId();
                etsis_cache_flush_namespace('majr');
                etsis_logger_activity_log_write('New Record', 'Major', _filter_input_string(INPUT_POST, 'majorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'majorCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/major' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/major', [
            'title' => 'Major',
            'major' => $q
        ]);
    });

    $app->match('GET|POST', '/major/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $major = $app->db->major();
                foreach ($app->req->post as $k => $v) {
                    $major->$k = $v;
                }
                $major->where('id = ?', $id);
                $major->update();

                etsis_cache_flush_namespace('majr');
                etsis_logger_activity_log_write('Update Record', 'Major', _filter_input_string(INPUT_POST, 'majorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'majorCode')) . ')', get_persondata('uname'));
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
            $major = $app->db->major()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-major', [
                'title' => 'View Major',
                'major' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/minor/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $minor = $app->db->minor();
                foreach ($app->req->post as $k => $v) {
                    $minor->$k = $v;
                }
                $minor->save();

                $_id = $minor->lastInsertId();
                etsis_cache_flush_namespace('minr');
                etsis_logger_activity_log_write('New Record', 'Minor', _filter_input_string(INPUT_POST, 'minorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'minorCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/minor' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/minor', [
            'title' => 'Minor',
            'minor' => $q
        ]);
    });

    $app->match('GET|POST', '/minor/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $minor = $app->db->minor();
                foreach ($app->req->post as $k => $v) {
                    $minor->$k = $v;
                }
                $minor->where('id = ?', $id);
                $minor->update();

                etsis_cache_flush_namespace('minr');
                etsis_logger_activity_log_write('Update Record', 'Minor', _filter_input_string(INPUT_POST, 'minorName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'minorCode')) . ')', get_persondata('uname'));
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
            $minor = $app->db->minor()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-minor', [
                'title' => 'View Minor',
                'minor' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/ccd/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $ccd = $app->db->ccd();
                foreach ($app->req->post as $k => $v) {
                    $ccd->$k = $v;
                }
                $ccd->save();

                $_id = $ccd->lastInsertId();
                etsis_cache_flush_namespace('ccd');
                etsis_logger_activity_log_write('New Record', 'CCD', _filter_input_string(INPUT_POST, 'ccdName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'ccdCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/ccd' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/ccd', [
            'title' => 'CCD',
            'ccd' => $q
        ]);
    });

    $app->match('GET|POST', '/ccd/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $ccd = $app->db->ccd();
                foreach ($app->req->post as $k => $v) {
                    $ccd->$k = $v;
                }
                $ccd->where('id = ?', $id);
                $ccd->update();

                etsis_cache_flush_namespace('ccd');
                etsis_logger_activity_log_write('Update Record', 'CCD', _filter_input_string(INPUT_POST, 'ccdName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'ccdCode')) . ')', get_persondata('uname'));
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
            $ccd = $app->db->ccd()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-ccd', [
                'title' => 'View CCD',
                'ccd' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/specialization/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $spec = $app->db->specialization();
                foreach ($app->req->post as $k => $v) {
                    $spec->$k = $v;
                }
                $spec->save();

                $_id = $spec->lastInsertId();
                etsis_cache_flush_namespace('spec');
                etsis_logger_activity_log_write('New Record', 'Specialization', _filter_input_string(INPUT_POST, 'specName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'specCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/specialization' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/specialization', [
            'title' => 'Specialization',
            'spec' => $q
        ]);
    });

    $app->match('GET|POST', '/specialization/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $spec = $app->db->specialization();
                foreach ($app->req->post as $k => $v) {
                    $spec->$k = $v;
                }
                $spec->where('id = ?', $id);
                $spec->update();

                etsis_cache_flush_namespace('spec');
                etsis_logger_activity_log_write('Update Record', 'Specialization', _filter_input_string(INPUT_POST, 'specName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'specCode')) . ')', get_persondata('uname'));
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
            $spec = $app->db->specialization()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-specialization', [
                'title' => 'View Specialization',
                'spec' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/cip/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $cip = $app->db->cip();
                foreach ($app->req->post as $k => $v) {
                    $cip->$k = $v;
                }
                $cip->save();

                $_id = $cip->lastInsertId();
                etsis_cache_flush_namespace('cip');
                etsis_logger_activity_log_write('New Record', 'CIP', _filter_input_string(INPUT_POST, 'cipName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'cipCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/cip' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/cip', [
            'title' => 'CIP',
            'cip' => $q
        ]);
    });

    $app->match('GET|POST', '/cip/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $cip = $app->db->cip();
                foreach ($app->req->post as $k => $v) {
                    $cip->$k = $v;
                }
                $cip->where('id = ?', $id);
                $cip->update();

                etsis_cache_flush_namespace('cip');
                etsis_logger_activity_log_write('Update Record', 'CIP', _filter_input_string(INPUT_POST, 'cipName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'cipCode')) . ')', get_persondata('uname'));
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
            $cip = $app->db->cip()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-cip', [
                'title' => 'View CIP',
                'cip' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/rest/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $rest = $app->db->rest();
                foreach ($app->req->post as $k => $v) {
                    $rest->$k = $v;
                }
                $rest->save();

                $_id = $rest->lastInsertId();
                etsis_cache_flush_namespace('rest');
                etsis_logger_activity_log_write('New Record', 'Restriction', $app->req->post['code'], get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/rest' . '/' . $_id . '/');
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
            $rest = $app->db->rest()
                ->select('rest.*,department.deptName')
                ->_join('department', 'rest.deptCode = department.deptCode')
                ->orderBy('code');

            $q = etsis_cache_get('rest', 'rest');
            if (empty($q)) {
                $q = $rest->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('rest', $q, 'rest');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/rest', [
            'title' => 'Restriction',
            'rest' => $q
        ]);
    });

    $app->match('GET|POST', '/rest/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $rest = $app->db->rest();
                foreach ($app->req->post as $k => $v) {
                    $rest->$k = $v;
                }
                $rest->where('id = ?', $id);
                $rest->update();

                etsis_cache_flush_namespace('rest');
                etsis_logger_activity_log_write('Update Record', 'Restriction', $app->req->post['code'], get_persondata('uname'));
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
            $rest = $app->db->rest()
                ->where('id = ?', $id);

            $q = etsis_cache_get($id, 'rest');
            if (empty($q)) {
                $q = $rest->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'rest');
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
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-rest', [
                'title' => 'View Restriction',
                'rest' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/location/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $loc = $app->db->location();
                foreach ($app->req->post as $k => $v) {
                    $loc->$k = $v;
                }
                $loc->save();

                $_id = $loc->lastInsertId();
                etsis_cache_flush_namespace('loc');
                etsis_logger_activity_log_write('New Record', 'Location', _filter_input_string(INPUT_POST, 'locationCode') . ' (' . _trim(_filter_input_string(INPUT_POST, 'locationCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/location' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/location', [
            'title' => 'Location',
            'location' => $q
        ]);
    });

    $app->match('GET|POST', '/location/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $loc = $app->db->location();
                foreach ($app->req->post as $k => $v) {
                    $loc->$k = $v;
                }
                $loc->where('id = ?', $id);
                $loc->update();

                etsis_cache_flush_namespace('loc');
                etsis_logger_activity_log_write('Update Record', 'Location Code', _filter_input_string(INPUT_POST, 'locationCode') . ' (' . _trim(_filter_input_string(INPUT_POST, 'locationCode')) . ')', get_persondata('uname'));
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
            $loc = $app->db->location()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-location', [
                'title' => 'View Location',
                'location' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/building/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $build = $app->db->building();
                foreach ($app->req->post as $k => $v) {
                    $build->$k = $v;
                }
                $build->save();

                $_id = $build->lastInsertId();
                etsis_cache_flush_namespace('bldg');
                etsis_logger_activity_log_write('New Record', 'Building', _filter_input_string(INPUT_POST, 'buildingName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'buildingCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/building' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/building', [
            'title' => 'Building',
            'build' => $q
        ]);
    });

    $app->match('GET|POST', '/building/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $build = $app->db->building();
                foreach ($app->req->post as $k => $v) {
                    $build->$k = $v;
                }
                $build->where('id = ?', $id);
                $build->update();

                etsis_cache_flush_namespace('bldg');
                etsis_logger_activity_log_write('Update Record', 'Building', _filter_input_string(INPUT_POST, 'buildingName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'buildingCode')) . ')', get_persondata('uname'));
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
            $build = $app->db->building()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-building', [
                'title' => 'View Building',
                'build' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/room/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $room = $app->db->room();
                foreach ($app->req->post as $k => $v) {
                    $room->$k = $v;
                }
                $room->save();

                $_id = $room->lastInsertId();
                etsis_cache_flush_namespace('room');
                etsis_logger_activity_log_write('New Record', 'Room', _filter_input_string(INPUT_POST, 'roomCode'), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/room' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/room', [
            'title' => 'Room',
            'room' => $q
        ]);
    });

    $app->match('GET|POST', '/room/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $room = $app->db->room();
                foreach ($app->req->post as $k => $v) {
                    $room->$k = $v;
                }
                $room->where('id = ?', $id);
                $room->update();

                etsis_cache_flush_namespace('room');
                etsis_logger_activity_log_write('Update Record', 'Room', _filter_input_string(INPUT_POST, 'roomCode'), get_persondata('uname'));
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
            $room = $app->db->room()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-room', [
                'title' => 'View Room',
                'room' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/school/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $school = $app->db->school();
                foreach ($app->req->post as $k => $v) {
                    $school->$k = $v;
                }
                $school->save();

                $_id = $school->lastInsertId();
                etsis_cache_flush_namespace('sch');
                etsis_logger_activity_log_write('New Record', 'School', _filter_input_string(INPUT_POST, 'schoolName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'schoolCode')) . ')', get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/school' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/school', [
            'title' => 'School',
            'school' => $q
        ]);
    });

    $app->match('GET|POST', '/school/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $school = $app->db->school();
                foreach ($app->req->post as $k => $v) {
                    $school->$k = $v;
                }
                $school->where('id = ?', $id);
                $school->update();

                etsis_cache_flush_namespace('sch');
                etsis_logger_activity_log_write('Update Record', 'School', _filter_input_string(INPUT_POST, 'schoolName') . ' (' . _trim(_filter_input_string(INPUT_POST, 'schoolCode')) . ')', get_persondata('uname'));
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
            $school = $app->db->school()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-school', [
                'title' => 'View School',
                'school' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/grade-scale/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $gs = $app->db->grade_scale();
                foreach ($app->req->post as $k => $v) {
                    $gs->$k = $v;
                }
                $gs->save();

                $_id = $gs->lastInsertId();
                etsis_cache_flush_namespace('grsc');
                etsis_logger_activity_log_write('New Record', 'Grade Scale', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/grade-scale' . '/' . $_id . '/');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/grade-scale', [
            'title' => 'Grade Scale',
            'scale' => $q
        ]);
    });

    $app->match('GET|POST', '/grade-scale/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $gs = $app->db->grade_scale();
                foreach ($app->req->post as $k => $v) {
                    $gs->$k = $v;
                }
                $gs->where('id = ?', $id);
                $gs->update();

                etsis_cache_flush_namespace('grsc');
                etsis_logger_activity_log_write('Update Record', 'Grade Scale', _filter_input_string(INPUT_POST, 'grade'), get_persondata('uname'));
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
            $gs = $app->db->grade_scale()
                ->where('id = ?', $id);

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

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['id']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-grade-scale', [
                'title' => 'View Grade Scale',
                'scale' => $q
            ]);
        }
    });

    $app->match('GET|POST', '/aclv/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $aclv = $app->db->aclv();
                $aclv->code = _trim((string) $app->req->post['code']);
                $aclv->name = (string) $app->req->post['name'];
                $aclv->ht_creds = (double) $app->req->post['ht_creds'];
                $aclv->ft_creds = (double) $app->req->post['ft_creds'];
                $aclv->ovr_creds = (double) $app->req->post['ovr_creds'];
                $aclv->grad_level = (string) $app->req->post['grad_level'];
                $aclv->comp_months = (int) $app->req->post['comp_months'];
                $aclv->save();

                $_id = $aclv->lastInsertId();
                etsis_cache_flush_namespace('aclv');
                etsis_logger_activity_log_write('New Record', 'Academic Level (ACLV)', _filter_input_string(INPUT_POST, 'code'), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'form/aclv' . '/' . $_id . '/');
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
            $aclv = $app->db->aclv()
                ->orderBy('code');

            $q = etsis_cache_get('aclv', 'aclv');
            if (empty($q)) {
                $q = $aclv->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add('aclv', $q, 'aclv');
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('form/aclv', [
            'title' => 'Academic Level (ACLV)',
            'aclv' => $q
        ]);
    });

    $app->match('GET|POST', '/aclv/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $aclv = $app->db->aclv();
                $aclv->code = _trim((string) $app->req->post['code']);
                $aclv->name = (string) $app->req->post['name'];
                $aclv->ht_creds = (double) $app->req->post['ht_creds'];
                $aclv->ft_creds = (double) $app->req->post['ft_creds'];
                $aclv->ovr_creds = (double) $app->req->post['ovr_creds'];
                $aclv->grad_level = (string) $app->req->post['grad_level'];
                $aclv->comp_months = (int) $app->req->post['comp_months'];
                $aclv->where('id = ?', $id);
                $aclv->update();

                update_aclv_code_on_update('stld', $id, _trim((string) $app->req->post['code']));
                update_aclv_code_on_update('clvr', $id, _trim((string) $app->req->post['code']));
                update_aclv_code_on_update('alst', $id, _trim((string) $app->req->post['code']));
                etsis_cache_flush_namespace('aclv');
                etsis_logger_activity_log_write('Update Record', 'Academic Level (ACLV)', _filter_input_string(INPUT_POST, 'code'), get_persondata('uname'));
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
            $aclv = $app->db->aclv()
                ->where('id = ?', $id);

            $q = etsis_cache_get($id, 'aclv');
            if (empty($q)) {
                $q = $aclv->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($id, $q, 'aclv');
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
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count(_h($q[0]['id'])) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('form/view-aclv', [
                'title' => _h($q[0]['code']) . ' - ' . _h($q[0]['name']) . ' ' . _t('Academic Level'),
                'aclv' => $q
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/aclv/(\d+)/stld/', function () {
        if (!hasPermission('manage_business_rules')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/aclv/(\d+)/stld/', function ($id) use($app) {
        if ($app->req->isPost()) {
            $size = count($app->req->post['rule']);
            $i = 0;
            while ($i < $size) {
                if ($app->req->post['id'][$i] == null) {
                    try {
                        $rlde = get_rule_by_code((string) $app->req->post['rule'][$i]);
                        $stld = Node::table('stld');
                        $stld->rid = (int) _h($rlde->id);
                        $stld->aid = (int) $id;
                        $stld->rule = (string) $app->req->post['rule'][$i];
                        $stld->value = (string) $app->req->post['value'][$i];
                        $stld->level = _trim((string) $app->req->post['level']);
                        $stld->save();
                    } catch (NodeQException $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    } catch (Exception $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    }
                }
                ++$i;
            }

            $id_size = count($app->req->post['rule']);
            $t = 0;
            while ($t < $id_size) {
                if ($app->req->post['id'][$t] > 0) {
                    try {
                        $rlde = get_rule_by_code((string) $app->req->post['rule'][$t]);
                        $stld = Node::table('stld')->find($app->req->post['id'][$t]);
                        $stld->rid = (int) _h($rlde->id);
                        $stld->aid = (int) $id;
                        $stld->rule = (string) $app->req->post['rule'][$t];
                        $stld->value = (string) $app->req->post['value'][$t];
                        $stld->level = _trim((string) $app->req->post['level']);
                        $stld->save();
                    } catch (NodeQException $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    } catch (Exception $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    }
                }
                ++$t;
            }
            _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
        }

        try {
            $aclv = $app->db->aclv()->findOne($id);
            $stu = $app->db->student()
                ->where('status = "A"')
                ->find();
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

        try {
            $stld = Node::table('stld')->with('rlde')->where('aid', '=', $id)->findAll();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($aclv == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($aclv) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count(_h($aclv->id)) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datatables');

            $app->view->display('form/stld', [
                'title' => _t('Student Load Rules'),
                'aclv' => $aclv,
                'stld' => $stld,
                'stu' => $stu
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('POST', '/aclv/(\d+)/stld/test/', function () {
        if (!hasPermission('manage_business_rules')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->post('/aclv/(\d+)/stld/test/', function ($id) use($app) {
        try {
            $rlde = Node::table('rlde')->where('code', '=', $app->req->post['rule'])->find();

            try {
                $stld = $app->db->query(
                    "SELECT sttr.stuID FROM $rlde->file"
                    . " INNER JOIN term ON sttr.termCode = term.termCode"
                    . " WHERE sttr.termCode = ?"
                    . " AND $rlde->rule", [$app->req->post['termCode']]
                );

                $q = $stld->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                $a = [];
                foreach ($q as $row) {
                    $a[] = _escape($row['stuID']);
                }

                if (!in_array($app->req->post['stuID'], $a)) {
                    _etsis_flash()->error(sprintf(_t('<strong>%s</strong> did not pass the <strong>%s</strong> rule for the <strong>%s</strong> term.'), get_name($app->req->post['stuID']), _escape($rlde->description), $app->req->post['termCode']), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'stld' . '/');
                } else {
                    _etsis_flash()->success(sprintf(_t('<strong>%s</strong> passed the <strong>%s</strong> rule for the <strong>%s</strong> term.'), get_name($app->req->post['stuID']), _escape($rlde->description), $app->req->post['termCode']), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'stld' . '/');
                }
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'stld' . '/');
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'stld' . '/');
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'stld' . '/');
            }
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error($e->getMessage(), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'stld' . '/');
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error($e->getMessage(), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'stld' . '/');
        }
    });

    $app->match('GET|POST', '/aclv/(\d+)/clas/', function ($id) use($app) {
        if ($app->req->isPost()) {
            $size = count($app->req->post['code']);
            $i = 0;
            while ($i < $size) {
                if ($app->req->post['id'][$i] == null) {
                    try {
                        $clas = $app->db->clas();
                        $clas->code = _trim((string) $app->req->post['code'][$i]);
                        $clas->name = (string) $app->req->post['name'][$i];
                        $clas->acadLevelCode = _trim((string) $app->req->post['acadLevelCode']);
                        $clas->save();
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
                ++$i;
            }

            $id_size = count($app->req->post['code']);
            $t = 0;
            while ($t < $id_size) {
                if ($app->req->post['id'][$t] > 0) {
                    try {
                        $clas = $app->db->clas();
                        $clas->code = _trim((string) $app->req->post['code'][$t]);
                        $clas->name = (string) $app->req->post['name'][$t];
                        $clas->acadLevelCode = _trim((string) $app->req->post['acadLevelCode']);
                        $clas->where('id = ?', (int) $app->req->post['id'][$t]);
                        $clas->update();
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
                ++$t;
            }
            _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
        }

        try {
            $aclv = $app->db->aclv()->findOne($id);
            $clas = $app->db->clas()->where('acadLevelCode = ?', $aclv->code);
            $q = $clas->find(function ($data) {
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
        if ($aclv == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($aclv) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count(_h($aclv->id)) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datatables');

            $app->view->display('form/clas', [
                'title' => _t('Class Level (CLAS)'),
                'aclv' => $aclv,
                'clas' => $q
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/aclv/(\d+)/clvr/', function () {
        if (!hasPermission('manage_business_rules')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/aclv/(\d+)/clvr/', function ($id) use($app) {
        if ($app->req->isPost()) {
            $size = count($app->req->post['rule']);
            $i = 0;
            while ($i < $size) {
                if ($app->req->post['id'][$i] == null) {
                    try {
                        $rlde = get_rule_by_code((string) $app->req->post['rule'][$i]);
                        $clvr = Node::table('clvr');
                        $clvr->rid = (int) _h($rlde->id);
                        $clvr->aid = (int) $id;
                        $clvr->rule = (string) $app->req->post['rule'][$i];
                        $clvr->value = (string) $app->req->post['value'][$i];
                        $clvr->level = _trim((string) $app->req->post['level']);
                        $clvr->save();
                    } catch (NodeQException $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    } catch (Exception $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    }
                }
                ++$i;
            }

            $id_size = count($app->req->post['rule']);
            $t = 0;
            while ($t < $id_size) {
                if ($app->req->post['id'][$t] > 0) {
                    try {
                        $rlde = get_rule_by_code((string) $app->req->post['rule'][$t]);
                        $clvr = Node::table('clvr')->find($app->req->post['id'][$t]);
                        $clvr->rid = (int) _h($rlde->id);
                        $clvr->aid = (int) $id;
                        $clvr->rule = (string) $app->req->post['rule'][$t];
                        $clvr->value = (string) $app->req->post['value'][$t];
                        $clvr->level = _trim((string) $app->req->post['level']);
                        $clvr->save();
                    } catch (NodeQException $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    } catch (Exception $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    }
                }
                ++$t;
            }

            _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
        }

        try {
            $aclv = $app->db->aclv()->findOne($id);
            $stu = $app->db->student()
                ->where('status = "A"')
                ->find();
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

        try {
            $clvr = Node::table('clvr')->with('rlde')->where('aid', '=', $id)->findAll();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($aclv == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($aclv) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count(_h($aclv->id)) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datatables');

            $app->view->display('form/clvr', [
                'title' => _t('Class Level Rules'),
                'aclv' => $aclv,
                'clvr' => $clvr,
                'stu' => $stu
            ]);
        }
    });

    $app->post('/aclv/(\d+)/clvr/test/', function ($id) use($app) {
        try {
            $rlde = Node::table('rlde')->where('code', '=', $app->req->post['rule'])->find();
            try {
                $clas = $app->db->query(
                    "SELECT v_scrd.stuID FROM $rlde->file"
                    . " INNER JOIN stal ON v_scrd.stuID = stal.stuID AND v_scrd.acadLevel = stal.acadLevelCode"
                    . " WHERE v_scrd.acadLevel = ?"
                    . " AND $rlde->rule"
                    . " AND (stal.endDate IS NULL"
                    . " OR stal.endDate <= '0000-00-00')", [$app->req->post['acadLevelCode']]
                );

                $q = $clas->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                $a = [];
                foreach ($q as $row) {
                    $a[] = _escape($row['stuID']);
                }

                if (!in_array($app->req->post['stuID'], $a)) {
                    _etsis_flash()->error(sprintf(_t('<strong>%s</strong> did not pass the <strong>%s</strong> rule.'), get_name($app->req->post['stuID']), _escape($rlde->description)), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'clvr' . '/');
                } else {
                    _etsis_flash()->success(sprintf(_t('<strong>%s</strong> passed the <strong>%s</strong> rule.'), get_name($app->req->post['stuID']), _escape($rlde->description)), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'clvr' . '/');
                }
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'clvr' . '/');
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'clvr' . '/');
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'clvr' . '/');
            }
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error($e->getMessage(), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'clvr' . '/');
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error($e->getMessage(), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'clvr' . '/');
        }
    });

    $app->match('GET|POST', '/aclv/(\d+)/alst/', function ($id) use($app) {
        if ($app->req->isPost()) {
            $size = count($app->req->post['rule']);
            $i = 0;
            while ($i < $size) {
                if ($app->req->post['id'][$i] == null) {
                    try {
                        $rlde = get_rule_by_code((string) $app->req->post['rule'][$i]);
                        $clvr = Node::table('alst');
                        $clvr->rid = (int) _h($rlde->id);
                        $clvr->aid = (int) $id;
                        $clvr->rule = (string) $app->req->post['rule'][$i];
                        $clvr->value = (string) $app->req->post['value'][$i];
                        $clvr->level = _trim((string) $app->req->post['level']);
                        $clvr->save();
                    } catch (NodeQException $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    } catch (Exception $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    }
                }
                ++$i;
            }

            $id_size = count($app->req->post['rule']);
            $t = 0;
            while ($t < $id_size) {
                if ($app->req->post['id'][$t] > 0) {
                    try {
                        $rlde = get_rule_by_code((string) $app->req->post['rule'][$t]);
                        $clvr = Node::table('alst')->find($app->req->post['id'][$t]);
                        $clvr->rid = (int) _h($rlde->id);
                        $clvr->aid = (int) $id;
                        $clvr->rule = (string) $app->req->post['rule'][$t];
                        $clvr->value = (string) $app->req->post['value'][$t];
                        $clvr->level = _trim((string) $app->req->post['level']);
                        $clvr->save();
                    } catch (NodeQException $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    } catch (Exception $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    }
                }
                ++$t;
            }

            _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
        }

        try {
            $aclv = $app->db->aclv()->findOne($id);
            $stu = $app->db->student()
                ->where('status = "A"')
                ->find();
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

        try {
            $alst = Node::table('alst')->with('rlde')->where('aid', '=', $id)->findAll();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($aclv == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($aclv) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count(_h($aclv->id)) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datatables');

            $app->view->display('form/alst', [
                'title' => _t('Academic Level Standing Rules'),
                'aclv' => $aclv,
                'alst' => $alst,
                'stu' => $stu
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('POST', '/aclv/(\d+)/alst/test/', function () {
        if (!hasPermission('manage_business_rules')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->post('/aclv/(\d+)/alst/test/', function ($id) use($app) {
        try {
            $rlde = Node::table('rlde')->where('code', '=', $app->req->post['rule'])->find();
            $aclv = $app->db->aclv()->findOne($id);
            try {
                $alst = $app->db->query(
                    "SELECT v_scrd.stuID FROM $rlde->file"
                    . " WHERE v_scrd.acadLevel = ?"
                    . " AND $rlde->rule", [_escape($aclv->code)]
                );

                $q = $alst->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                $a = [];
                foreach ($q as $row) {
                    $a[] = _escape($row['stuID']);
                }

                if (!in_array($app->req->post['stuID'], $a)) {
                    _etsis_flash()->error(sprintf(_t('<strong>%s</strong> did not pass the <strong>%s</strong> rule for the <strong>%s</strong> academic level.'), get_name($app->req->post['stuID']), _escape($rlde->description), _escape($aclv->code)), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'alst' . '/');
                } else {
                    _etsis_flash()->success(sprintf(_t('<strong>%s</strong> passed the <strong>%s</strong> rule for the <strong>%s</strong> academic level.'), get_name($app->req->post['stuID']), _escape($rlde->description), _escape($aclv->code)), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'alst' . '/');
                }
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'alst' . '/');
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'alst' . '/');
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'alst' . '/');
            }
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error($e->getMessage(), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'alst' . '/');
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error($e->getMessage(), get_base_url() . 'form/aclv' . '/' . $id . '/' . 'alst' . '/');
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET', '/aclv/(\d+)/stld/(\d+)/(\d+)/d/', function () {
        if (!hasPermission('manage_business_rules')) {
            _etsis_flash()->error(_t("You don't have the proper permission(s) to delete a student load rule."), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->get('/aclv/(\d+)/stld/(\d+)/(\d+)/d/', function ($aid, $rid, $id) {
        try {
            $stld = Node::table('stld');

            if ($stld->where('aid', '=', $aid)->where('rid', '=', $rid)->findAll()->count() > 0) {
                $stld->find($id)->delete();
                _etsis_flash()->success(_etsis_flash()->notice(200));
            }
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }
        etsis_redirect(get_base_url() . 'form/aclv/' . $aid . '/stld/');
    });

    /**
     * Before route check.
     */
    $app->before('GET', '/aclv/(\d+)/clvr/(\d+)/(\d+)/d/', function () {
        if (!hasPermission('manage_business_rules')) {
            _etsis_flash()->error(_t("You don't have the proper permission(s) to delete a class level rule."), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->get('/aclv/(\d+)/clvr/(\d+)/(\d+)/d/', function ($aid, $rid, $id) {
        try {
            $clvr = Node::table('clvr');

            if ($clvr->where('aid', '=', $aid)->where('rid', '=', $rid)->findAll()->count() > 0) {
                $clvr->find($id)->delete();
                _etsis_flash()->success(_etsis_flash()->notice(200));
            }
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }
        etsis_redirect(get_base_url() . 'form/aclv/' . $aid . '/clvr/');
    });

    /**
     * Before route check.
     */
    $app->before('GET', '/aclv/(\d+)/alst/(\d+)/(\d+)/d/', function () {
        if (!hasPermission('manage_business_rules')) {
            _etsis_flash()->error(_t("You don't have the proper permission(s) to delete an academic level standing rule."), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->get('/aclv/(\d+)/alst/(\d+)/(\d+)/d/', function ($aid, $rid, $id) {
        try {
            $alst = Node::table('alst');

            if ($alst->where('aid', '=', $aid)->where('rid', '=', $rid)->findAll()->count() > 0) {
                $alst->find($id)->delete();
                _etsis_flash()->success(_etsis_flash()->notice(200));
            }
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }
        etsis_redirect(get_base_url() . 'form/aclv/' . $aid . '/alst/');
    });

    $app->setError(function () use($app) {

        $app->view->display('error/404', [
            'title' => '404 Error'
        ]);
    });
});

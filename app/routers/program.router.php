<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Academic Program Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
/**
 * Before route checks to make sure the logged in user
 * us allowed to manage options/settings.
 */
$app->before('GET|POST', '/program(.*)', function() {
    if (!is_user_logged_in()) {
        redirect(get_base_url() . 'login' . '/');
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
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
];

$json_url = get_base_url() . 'api' . '/';

$logger = new \app\src\Log();
$flashNow = new \app\src\Core\etsis_Messages();

$app->group('/program', function() use ($app, $css, $js, $json_url, $logger, $flashNow) {

    $app->match('GET|POST', '/', function () use($app, $css, $js) {
        if ($app->req->isPost()) {
            $post = $_POST['prog'];
            $prog = $app->db->query("SELECT 
                    CASE currStatus 
                    WHEN 'A' THEN 'Active' 
                    WHEN 'I' THEN 'Inactive' 
                    WHEN 'P' THEN 'Pending' 
                    ELSE 'Obsolete' 
                    END AS 'Status', 
                    acadProgID,
                    acadProgCode,
                    acadProgTitle,
                    startDate,
                    endDate 
                FROM acad_program 
                WHERE acadProgCode LIKE ?", [ "%$post%"]
            );

            $q = $prog->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        }
        $app->view->display('program/index', [
            'title' => 'Search Academic Program',
            'cssArray' => $css,
            'jsArray' => $js,
            'prog' => $q
            ]
        );
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $flashNow) {
        $program = get_acad_program($id);

        if ($app->req->isPost()) {
            $prog = $app->db->acad_program();
            $prog->acadProgCode = $_POST['acadProgCode'];
            $prog->acadProgTitle = $_POST['acadProgTitle'];
            $prog->programDesc = $_POST['programDesc'];
            $prog->currStatus = $_POST['currStatus'];
            if ($program->currStatus !== $_POST['currStatus']) {
                $prog->statusDate = $app->db->NOW();
            }
            $prog->deptCode = $_POST['deptCode'];
            $prog->schoolCode = $_POST['schoolCode'];
            $prog->acadYearCode = $_POST['acadYearCode'];
            $prog->startDate = $_POST['startDate'];
            $prog->endDate = $_POST['endDate'];
            $prog->degreeCode = $_POST['degreeCode'];
            $prog->ccdCode = $_POST['ccdCode'];
            $prog->majorCode = $_POST['majorCode'];
            $prog->minorCode = $_POST['minorCode'];
            $prog->specCode = $_POST['specCode'];
            $prog->acadLevelCode = $_POST['acadLevelCode'];
            $prog->cipCode = $_POST['cipCode'];
            $prog->locationCode = $_POST['locationCode'];
            $prog->where('acadProgID = ?', $_POST['acadProgID']);

            /**
             * Fires during the update of an academic program.
             *
             * @since 6.1.10
             * @param array $prog Academic program object.
             */
            $app->hook->do_action('update_acad_program_db_table', $prog);

            if ($prog->update()) {
                etsis_cache_delete($id, 'prog');
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update', 'Acad Program', $program->acadProgCode, get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                $logger->setLog('Update Error', 'Acad Program', $program->acadProgCode, get_persondata('uname'));
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($program == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($program) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($program->acadProgID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('program/view', [
                'title' => $program->acadProgTitle . ' :: Academic Program',
                'cssArray' => $css,
                'jsArray' => $js,
                'prog' => $program
                ]
            );
        }
    });

    /**
     * Before route checks to make sure the logged in user
     * us allowed to manage options/settings.
     */
    $app->before('GET|POST', '/add/', function() {
        if (!hasPermission('add_acad_prog')) {
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

    $app->match('GET|POST', '/add/', function () use($app, $css, $js, $logger, $flashNow) {

        if ($app->req->isPost()) {
            $prog = $app->db->acad_program();
            $prog->acadProgCode = $_POST['acadProgCode'];
            $prog->acadProgTitle = $_POST['acadProgTitle'];
            $prog->programDesc = $_POST['programDesc'];
            $prog->currStatus = $_POST['currStatus'];
            $prog->statusDate = $app->db->NOW();
            $prog->approvedDate = $app->db->NOW();
            $prog->approvedBy = get_persondata('personID');
            $prog->deptCode = $_POST['deptCode'];
            $prog->schoolCode = $_POST['schoolCode'];
            $prog->acadYearCode = $_POST['acadYearCode'];
            $prog->startDate = $_POST['startDate'];
            $prog->endDate = $_POST['endDate'];
            $prog->degreeCode = $_POST['degreeCode'];
            $prog->ccdCode = $_POST['ccdCode'];
            $prog->majorCode = $_POST['majorCode'];
            $prog->minorCode = $_POST['minorCode'];
            $prog->specCode = $_POST['specCode'];
            $prog->acadLevelCode = $_POST['acadLevelCode'];
            $prog->cipCode = $_POST['cipCode'];
            $prog->locationCode = $_POST['locationCode'];

            /**
             * Fires during the saving/creating of an academic program.
             *
             * @since 6.1.10
             * @param array $prog Academic program object.
             */
            $app->hook->do_action('save_acad_program_db_table', $prog);

            if ($prog->save()) {
                $ID = $prog->lastInsertId();
                etsis_cache_flush_namespace('prog');
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Acad Program', $_POST['acadProgCode'], get_persondata('uname'));
                redirect(get_base_url() . 'program' . '/' . $ID . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        $app->view->display('program/add', [
            'title' => 'Add Academic Program',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    $app->post('/year/', function() use($app) {
        etsis_cache_flush_namespace('ayr');
        $year = $app->db->acad_year();
        foreach ($_POST as $k => $v) {
            $year->$k = $v;
        }
        $year->save();
        $ID = $year->lastInsertId();

        $acad = $app->db->acad_year()
            ->where('acadYearID = ?', $ID);
        $q = $acad->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        echo json_encode($q);
    });

    $app->post('/degree/', function() use($app) {
        etsis_cache_flush_namespace('deg');
        $deg = $app->db->degree();
        foreach ($_POST as $k => $v) {
            $deg->$k = $v;
        }
        $deg->save();
        $ID = $deg->lastInsertId();

        $degree = $app->db->degree()
            ->where('degreeID = ?', $ID);
        $q = $degree->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        echo json_encode($q);
    });

    $app->post('/ccd/', function() use($app) {
        etsis_cache_flush_namespace('ccd');
        $c = $app->db->ccd();
        foreach ($_POST as $k => $v) {
            $c->$k = $v;
        }
        $c->save();
        $ID = $c->lastInsertId();

        $ccd = $app->db->ccd()
            ->where('ccdID = ?', $ID);
        $q = $ccd->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        echo json_encode($q);
    });

    $app->post('/major/', function() use($app) {
        etsis_cache_flush_namespace('majr');
        $maj = $app->db->major();
        foreach ($_POST as $k => $v) {
            $maj->$k = $v;
        }
        $maj->save();
        $ID = $maj->lastInsertId();

        $major = $app->db->major()
            ->where('majorID = ?', $ID);
        $q = $major->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        echo json_encode($q);
    });

    $app->post('/minor/', function() use($app) {
        etsis_cache_flush_namespace('minr');
        $min = $app->db->minor();
        foreach ($_POST as $k => $v) {
            $min->$k = $v;
        }
        $min->save();
        $ID = $min->lastInsertId();

        $minor = $app->db->minor()
            ->where('minorID = ?', $ID);
        $q = $minor->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        echo json_encode($q);
    });

    $app->post('/spec/', function() use($app) {
        etsis_cache_flush_namespace('spec');
        $spec = $app->db->specialization();
        foreach ($_POST as $k => $v) {
            $spec->$k = $v;
        }
        $spec->save();
        $ID = $spec->lastInsertId();

        $specialization = $app->db->specialization()
            ->where('specID = ?', $ID);
        $q = $specialization->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        echo json_encode($q);
    });

    $app->post('/cip/', function() use($app) {
        etsis_cache_flush_namespace('cip');
        $c = $app->db->cip();
        foreach ($_POST as $k => $v) {
            $c->$k = $v;
        }
        $c->save();
        $ID = $c->lastInsertId();

        $cip = $app->db->cip()
            ->where('cipID = ?', $ID);
        $q = $cip->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        echo json_encode($q);
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

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

$json_url = url('/v1/');

$logger = new \app\src\Log();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();

$app->group('/program', function() use ($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {

    /**
     * Before route checks to make sure the logged in user
     * us allowed to manage options/settings.
     */
    $app->before('GET|POST', '/.*', function() {
        if (!hasPermission('access_acad_prog_screen')) {
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

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {
        $json = _file_get_contents($json_url . 'acad_program/acadProgID/' . $id . '/');
        $decode = json_decode($json, true);

        if ($app->req->isPost()) {
            $prog = $app->db->acad_program();
            $prog->acadProgCode = $_POST['acadProgCode'];
            $prog->acadProgTitle = $_POST['acadProgTitle'];
            $prog->programDesc = $_POST['programDesc'];
            $prog->currStatus = $_POST['currStatus'];
            if ($decode[0]['currStatus'] !== $_POST['currStatus']) {
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
            if ($prog->update()) {
                $dbcache->clearCache("acad_program-" . $_POST['acadProgID']);
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('Update', 'Acad Program', $decode[0]['acadProgCode'], get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                $logger->setLog('Update Error', 'Acad Program', $decode[0]['acadProgCode'], get_persondata('uname'));
            }
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
         */ elseif (count($decode[0]['acadProgID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('program/view', [
                'title' => $decode[0]['acadProgTitle'] . ' :: Academic Program',
                'cssArray' => $css,
                'jsArray' => $js,
                'prog' => $decode
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

    $app->match('GET|POST', '/add/', function () use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {

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
            if ($prog->save()) {
                $ID = $prog->lastInsertId();
                $app->flash('success_message', $flashNow->notice(200));
                $logger->setLog('New Record', 'Acad Program', $_POST['acadProgCode'], get_persondata('uname'));
                redirect(url('/program/') . $ID . '/');
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
});

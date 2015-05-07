<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Before router check.
 */
$app->before('GET|POST', '/institution.*', function() {

    /**
     * If user is logged in and the lockscreen cookie is set, 
     * redirect user to the lock screen until he/she enters 
     * his/her password to gain access.
     */
    if (isset($_COOKIE['SCREENLOCK'])) {
        redirect(url('/lock/'));
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

$json_url = url('/v1/');

$logger = new \app\src\Log();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();

$app->group('/institution', function () use($app, $css, $js, $json_url, $logger, $dbcache, $flashNow) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/.*', function() {
        if (!hasPermission('access_institutions_screen')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/', function () use($app, $css, $js) {
        if ($app->req->isPost()) {
            $post = $_POST['inst'];
            $inst = $app->db->institution()
                ->whereLike('instName', "%$post%")->_or_()
                ->whereLike('fice_ceeb', "%$post%");

            $q = $inst->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        }
        $app->view->display('institution/index', [
            'title' => 'Search Institution',
            'cssArray' => $css,
            'jsArray' => $js,
            'search' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/add/', function() {
        if (!hasPermission('add_institution')) {
            redirect(url('/dashboard/'));
        }
    });

    $app->match('GET|POST', '/add/', function () use($app, $css, $js, $logger, $dbcache, $flashNow) {

        if ($app->req->isPost()) {
            $inst = $app->db->institution();
            $inst->fice_ceeb = $_POST['fice_ceeb'];
            $inst->instType = $_POST['instType'];
            $inst->instName = $_POST['instName'];
            $inst->city = $_POST['city'];
            $inst->state = $_POST['state'];
            $inst->country = $_POST['country'];
            if ($inst->save()) {
                $ID = $inst->lastInsertId();
                $logger->setLog('New Record', 'Institution', $_POST['instName'] . ' ' . $_POST['fice_ceeb'], get_persondata('uname'));
                $app->flash('success_message', $flashNow->notice(200));
                redirect(url('/institution/') . $ID . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }
        $app->view->display('institution/add', [
            'title' => 'Add Institution',
            'cssArray' => $css,
            'jsArray' => $js,
            'inst' => $decode
            ]
        );
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'institution/institutionID/' . (int) $id . '/');
        $decode = json_decode($json, true);

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
         */ elseif (count($decode[0]['institutionID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('institution/view', [
                'title' => $decode[0]['instName'],
                'cssArray' => $css,
                'jsArray' => $js,
                'inst' => $decode
                ]
            );
        }
    });
});

<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Dashboard Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

/**
 * Before route check.
 */
$app->before('GET|POST', '/dashboard(.*)', function() {
    if (!hasPermission('access_dashboard')) {
        redirect(url('/'));
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

$logger = new \app\src\Log();
$cache = new \app\src\Cache();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();

$app->get('/dashboard', function () use($app) {

    $css = [ 'css/admin/module.admin.page.index.min.css'];

    $js = [
        'components/modules/admin/charts/flot/assets/lib/jquery.flot.js?v=v2.1.0',
        'components/modules/admin/charts/flot/assets/lib/jquery.flot.resize.js?v=v2.1.0',
        'components/modules/admin/charts/flot/assets/lib/plugins/jquery.flot.tooltip.min.js?v=v2.1.0',
        'components/modules/admin/charts/flot/assets/custom/js/flotcharts.common.js?v=v2.1.0',
        'components/modules/admin/charts/flot/assets/custom/js/flotchart-simple.init.js?v=v2.1.0',
        'components/modules/admin/charts/flot/custom/chart.js',
        'components/modules/admin/charts/flot/custom/js/custom-flot.js'
    ];

    $stuProg = $app->db->stu_program()
        ->select('COUNT(stu_program.stuProgID) as ProgCount,stu_program.acadProgCode')
        ->_join('acad_program', 'stu_program.acadProgCode = b.acadProgCode', 'b')
        ->where('stu_program.currStatus <> "G"')
        ->groupBy('stu_program.acadProgCode')
        ->orderBY('stu_program.acadProgCode', 'DESC')
        ->limit(10);

    $prog = $stuProg->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    $stuDept = $app->db->person()
        ->select('SUM(person.gender="M") AS Male,SUM(person.gender="F") AS Female,d.deptCode')
        ->_join('stu_program', 'person.personID = b.stuID', 'b')
        ->_join('acad_program', 'b.acadProgCode = c.acadProgCode', 'c')
        ->_join('department', 'c.deptCode = d.deptCode', 'd')
        ->where('b.startDate = (SELECT MAX(startDate) FROM stu_program WHERE stuID = b.stuID)')->_and_()
        ->where('b.currStatus = "A"')->_and_()
        ->where('d.deptTypeCode = "ACAD"')
        ->groupBy('d.deptCode')
        ->orderBy('d.deptCode', 'DESC')
        ->limit(10);

    $dept = $stuDept->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    $app->view->display('dashboard/index', [
        'title' => 'Dashboard',
        'cssArray' => $css,
        'jsArray' => $js,
        'prog' => $prog,
        'dept' => $dept
        ]
    );
});

$app->post('/dashboard/search/', function () use($app) {
    $acro = $_POST['screen'];
    $screen = explode(" ", $acro);

    $acryn = $app->db->screen()->where('code = ?', $screen[0])->limit(1);
    $q = $acryn->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    $a = [];
    foreach ($q as $r) {
        $a[] = $r;
    }

    if (!$r['relativeURL']) {
        redirect(url('/') . 'err/screen-error?code=' . _h($screen[0]));
    } else {
        redirect(url('/') . $r['relativeURL']);
    }
});

$app->get('/dashboard/support/', function () use($app) {
    $app->view->display('dashboard/support', ['title' => 'Online Support']);
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/dashboard/update/', function() {
    if (!hasPermission('edit_settings')) {
        redirect(url('/dashboard/'));
    }
});

$app->match('GET|POST', '/dashboard/update/', function () use($app) {
    $app->view->display('dashboard/update', [
        'title' => 'Automatic Update'
        ]
    );
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/dashboard/upgrade/', function() {
    if (!hasPermission('edit_settings')) {
        redirect(url('/dashboard/'));
    }
});

$app->match('GET|POST', '/dashboard/upgrade/', function () use($app) {
    $app->view->display('dashboard/upgrade', ['title' => 'Database Upgrade']);
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/dashboard/modules/', function() {
    if (!hasPermission('access_plugin_screen')) {
        redirect(url('/dashboard/'));
    }
});

$app->get('/dashboard/modules/', function () use($app) {
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

    $app->view->display('dashboard/modules', [
        'title' => 'System Modules',
        'cssArray' => $css,
        'jsArray' => $js
        ]
    );
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/dashboard/install-module/', function() {
    if (!hasPermission('access_plugin_admin_page')) {
        redirect(url('/dashboard/'));
    }
});

$app->match('GET|POST', '/dashboard/install-module/', function () use($app) {

    $css = [ 'css/admin/module.admin.page.form_elements.min.css'];
    $js = [
        'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/jasny-fileupload/assets/js/bootstrap-fileupload.js?v=v2.1.0'
    ];

    if ($app->req->isPost()) {
        $name = explode(".", $_FILES["module_zip"]["name"]);
        $accepted_types = [ 'application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed'];

        foreach ($accepted_types as $mime_type) {
            if ($mime_type == $type) {
                $okay = true;
                break;
            }
        }

        $continue = strtolower($name[1]) == 'zip' ? true : false;

        if (!$continue) {
            $app->flash('error_message', _t('The file you are trying to upload is not the accepted file type. Please try again.'));
        }
        $target_path = BASE_PATH . $_FILES["module_zip"]["name"];
        if (move_uploaded_file($_FILES["module_zip"]["tmp_name"], $target_path)) {
            $zip = new \ZipArchive();
            $x = $zip->open($target_path);
            if ($x === true) {
                $zip->extractTo(BASE_PATH);
                $zip->close();
                unlink($target_path);
            }
            $app->flash('success_message', _t('The module was uploaded and installed properly.'));
        } else {
            $app->flash('error_message', _t('There was a problem uploading the module. Please try again or check the module package.'));
        }
        redirect($app->req->server['HTTP_REFERER']);
    }

    $app->view->display('dashboard/install-module', [
        'title' => 'Install Modules',
        'cssArray' => $css,
        'jsArray' => $js
        ]
    );
});

$app->get('/dashboard/clearDBCache/', function () use($app, $dbcache) {
    $dbcache->purge();
    redirect($app->req->server['HTTP_REFERER']);
});

$app->get('/dashboard/clearScreenCache/', function () use($app, $cache) {
    $cache->purge();
    redirect($app->req->server['HTTP_REFERER']);
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Before route check.
 */
$app->before('GET|POST', '/plugins.*', function() {
    if (!hasPermission('access_plugin_screen')) {
        redirect(url('/dashboard/'));
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
    'components/modules/admin/forms/elements/bootstrap-timepicker/assets/lib/js/bootstrap-timepicker.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-timepicker/assets/custom/js/bootstrap-timepicker.init.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/jCombo/jquery.jCombo.min.js'
];

$app->group('/plugins', function() use ($app, $css, $js) {

    $app->get('/', function () use($app, $css, $js) {

        $app->view->display('plugins/index', [
            'title' => 'Plugins',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    $app->get('/activate/([^/]+)/', function($plugin) use($app) {
        $app->hook->{'activate_plugin'}($plugin);
        redirect($app->req->server['HTTP_REFERER']);
    });

    $app->get('/deactivate/([^/]+)/', function($plugin) use($app) {
        $app->hook->{'deactivate_plugin'}($plugin);
        redirect($app->req->server['HTTP_REFERER']);
    });

    $app->match('GET|POST', '/options/', function() use($app, $css, $js) {
        $app->view->display('plugins/options', [
            'title' => 'Plugin Options',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    $app->setError(function() use($app) {

        $app->res->_format('json', 404);
    });
});

<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use \app\src\Core\NodeQ\etsis_NodeQ as Node;

/**
 * Rule Definition Router
 *
 * @license GPLv3
 *         
 * @since 5.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
/**
 * Before route check.
 */
$app->before('GET|POST', '/rlde(.*)', function () {
    if (!hasPermission('access_forms') || !hasPermission('access_report_screen') || !hasPermission('access_save_query_screens')) {
        redirect(get_base_url());
    }
});

$flashNow = new \app\src\Core\etsis_Messages();

$app->match('GET|POST', '/rlde', function () use($app) {

    $css = [
        'css/admin/module.admin.page.tables.min.css'
    ];

    $js = [
        'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
        'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
    ];

    $rules = Node::table('rlde')->findAll();

    $app->view->display('rlde/index', [
        'title' => 'Rule Definition (RLDE)',
        'cssArray' => $css,
        'jsArray' => $js,
        'rules' => $rules
    ]);
});

$app->match('GET|POST', '/rlde/add/', function () use($app, $flashNow) {

    if ($app->req->isPost()) {
        $rlde = Node::table('rlde');
        $rlde->description = (string) $app->req->_post('description');
        $rlde->code = _trim((string) $app->req->_post('code'));
        $rlde->dept = (string) $app->req->_post('dept');
        $rlde->file = (string) $app->req->_post('file');
        $rlde->comment = (string) $app->req->_post('comment');
        $rlde->rule = (string) $app->req->_post('rule');
        $rlde->save();

        if ($rlde) {
            $ID = $rlde->lastId();
            $app->flash('success_message', $flashNow->notice(200));
            redirect(get_base_url() . 'rlde' . '/' . $ID . '/');
        } else {
            $app->flash('error_message', $flashNow->notice(409));
            redirect($app->req->server['HTTP_REFERER']);
        }
    }

    $css = [
        'css/admin/module.admin.page.index.min.css',
        'plugins/querybuilder/bootstrap-select/css/bootstrap-select.min.css',
        'plugins/querybuilder/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',
        'plugins/querybuilder/selectize/css/selectize.bootstrap3.css',
        'plugins/querybuilder/css/query-builder.default.css'
    ];

    $js = [
        'components/modules/admin/forms/elements/bootstrap-maxlength/bootstrap-maxlength.min.js',
        'components/modules/admin/forms/elements/bootstrap-maxlength/custom/js/custom.js'
    ];

    $table = $app->db->query('SHOW TABLES');
    $q = $table->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    $app->view->display('rlde/add', [
        'title' => 'Add Rule Definition',
        'cssArray' => $css,
        'jsArray' => $js,
        'table' => $q
    ]);
});

$app->match('GET|POST', '/rlde/(\d+)/', function ($id) use($app, $flashNow) {

    if ($app->req->isPost()) {
        $rlde = Node::table('rlde')->find($id);
        $rlde->description = (string) $app->req->_post('description');
        $rlde->code = _trim((string) $app->req->_post('code'));
        $rlde->dept = (string) $app->req->_post('dept');
        $rlde->file = (string) $app->req->_post('file');
        $rlde->comment = (string) $app->req->_post('comment');
        $rlde->rule = (string) $app->req->_post('rule');
        $rlde->save();

        if ($rlde) {       
            update_rlde_code_on_update('stld', $id, _trim((string) $app->req->_post('code')));
            update_rlde_code_on_update('clvr', $id, _trim((string) $app->req->_post('code')));
            $app->flash('success_message', $flashNow->notice(200));
        } else {
            $app->flash('error_message', $flashNow->notice(409));
        }
        redirect($app->req->server['HTTP_REFERER']);
    }

    $css = [
        'css/admin/module.admin.page.index.min.css',
        'plugins/querybuilder/bootstrap-select/css/bootstrap-select.min.css',
        'plugins/querybuilder/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',
        'plugins/querybuilder/selectize/css/selectize.bootstrap3.css',
        'plugins/querybuilder/css/query-builder.default.css'
    ];

    $js = [
        'components/modules/admin/forms/elements/bootstrap-maxlength/bootstrap-maxlength.min.js',
        'components/modules/admin/forms/elements/bootstrap-maxlength/custom/js/custom.js'
    ];

    $rule = Node::table('rlde')->where('id', '=', $id)->find();

    $app->view->display('rlde/view', [
        'title' => $rule->code . ' Rule',
        'cssArray' => $css,
        'jsArray' => $js,
        'rule' => $rule
    ]);
});

$app->before('GET', '/rlde/(\d+)/d/', function () use($app) {
    if (!hasPermission('access_forms') || !hasPermission('access_report_screen') || !hasPermission('access_save_query_screens')) {
        $app->flash('error_message', _t( "You don't have the proper permission(s) to delete a business rule." ));
        redirect($app->req->server['HTTP_REFERER']);
        exit();
    }
});

$app->get('/rlde/(\d+)/d/', function ($id) use($app, $flashNow) {
    $rlde = Node::table('rlde');
    
    if ($rlde->where('id','=',$id)->findAll()->count() > 0) {
        $rlde->find($id)->delete();
        Node::table('stld')->where('rid', '=', $id)->delete();
        Node::table('clvr')->where('rid', '=', $id)->delete();
        $app->flash('success_message', $flashNow->notice(200));
    } else {
        $app->flash('error_message', $flashNow->notice(409));
    }
    redirect($app->req->server['HTTP_REFERER']);
});

$app->setError(function () use($app) {

    $app->view->display('error/404', [
        'title' => '404 Error'
    ]);
});

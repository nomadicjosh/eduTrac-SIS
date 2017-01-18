<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use \app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\Exception;

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

$app->match('GET|POST', '/rlde', function () use($app) {
    try {
        $rules = Node::table('rlde')->findAll();
    } catch (NodeQException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    }

    etsis_register_style('form');
    etsis_register_style('table');
    etsis_register_script('select');
    etsis_register_script('select2');
    etsis_register_script('datatables');

    $app->view->display('rlde/index', [
        'title' => 'Rule Definition (RLDE)',
        'rules' => $rules
    ]);
});

$app->match('GET|POST', '/rlde/add/', function () use($app) {

    if ($app->req->isPost()) {
        try {
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
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'rlde' . '/' . $ID . '/');
            } else {
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        } catch (NodeQException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        }
    }

    try {
        $table = $app->db->query('SHOW TABLES');
        $q = $table->find(function ($data) {
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

    etsis_register_style('form');
    etsis_register_style('querybuilder');
    etsis_register_script('select');
    etsis_register_script('select2');
    etsis_register_script('maxlength');

    $app->view->display('rlde/add', [
        'title' => 'Add Rule Definition',
        'table' => $q
    ]);
});

$app->match('GET|POST', '/rlde/(\d+)/', function ($id) use($app) {

    if ($app->req->isPost()) {
        try {
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
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } else {
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        } catch (NodeQException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        }
    }

    try {
        $rule = Node::table('rlde')->where('id', '=', $id)->find();
    } catch (NodeQException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    }

    etsis_register_style('form');
    etsis_register_style('querybuilder');
    etsis_register_script('select');
    etsis_register_script('select2');
    etsis_register_script('maxlength');

    $app->view->display('rlde/view', [
        'title' => $rule->code . ' Rule',
        'rule' => $rule
    ]);
});

$app->before('GET', '/rlde/(\d+)/d/', function () use($app) {
    if (!hasPermission('access_forms') || !hasPermission('access_report_screen') || !hasPermission('access_save_query_screens')) {
        _etsis_flash()->error(_t("You don't have the proper permission(s) to delete a business rule."), $app->req->server['HTTP_REFERER']);
        exit();
    }
});

$app->get('/rlde/(\d+)/d/', function ($id) use($app) {
    try {
        $rlde = Node::table('rlde');

        if ($rlde->where('id', '=', $id)->findAll()->count() > 0) {
            $rlde->find($id)->delete();
            Node::table('stld')->where('rid', '=', $id)->delete();
            Node::table('clvr')->where('rid', '=', $id)->delete();
            _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
        } else {
            _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
        }
        redirect($app->req->server['HTTP_REFERER']);
    } catch (NodeQException $e) {
        _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
    }
});

$app->setError(function () use($app) {

    $app->view->display('error/404', [
        'title' => '404 Error'
    ]);
});

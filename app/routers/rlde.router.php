<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use \app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\Exception;
use Cascade\Cascade;

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
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
    }
    if (!hasPermission('manage_business_rules')) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        exit();
    }
});

$app->group('/rlde', function() use ($app) {

    $app->match('GET|POST', '/', function () use($app) {
        try {
            $rules = Node::table('rlde')->findAll();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
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

    $app->match('GET|POST', '/add/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $rlde = Node::table('rlde');
                $rlde->description = (string) $app->req->post['description'];
                $rlde->code = _trim((string) $app->req->post['code']);
                $rlde->dept = (string) $app->req->post['dept'];
                $rlde->file = (string) $app->req->post['file'];
                $rlde->comment = (string) $app->req->post['comment'];
                $rlde->rule = (string) $app->req->post['rule'];
                $rlde->save();

                $_id = $rlde->lastId();
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'rlde' . '/' . $_id . '/');
            } catch (NodeQException $e) {
                Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                _etsis_flash()->error(_etsis_flash()->notice(409));
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
        etsis_register_style('bootstrap-datepicker');
        etsis_register_style('querybuilder');

        $app->view->display('rlde/add', [
            'title' => 'Add Rule Definition',
            'table' => $q
        ]);
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app) {

        if ($app->req->isPost()) {
            try {
                $rlde = Node::table('rlde')->find($id);
                $rlde->description = (string) $app->req->post['description'];
                $rlde->code = _trim((string) $app->req->post['code']);
                $rlde->dept = (string) $app->req->post['dept'];
                $rlde->file = (string) $app->req->post['file'];
                $rlde->comment = (string) $app->req->post['comment'];
                $rlde->rule = (string) $app->req->post['rule'];
                $rlde->save();

                update_rlde_code_on_update('stld', $id, _trim((string) $app->req->post['code']));
                update_rlde_code_on_update('clvr', $id, _trim((string) $app->req->post['code']));
                update_rlde_code_on_update('rrsr', $id, _trim((string) $app->req->post['code']));
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } catch (NodeQException $e) {
                Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        }

        try {
            $rule = Node::table('rlde')->where('id', '=', $id)->find();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        etsis_register_style('form');
        etsis_register_style('bootstrap-datepicker');
        etsis_register_style('querybuilder');

        $app->view->display('rlde/view', [
            'title' => _h($rule->code) . ' Rule',
            'rule' => $rule
        ]);
    });

    $app->get('/(\d+)/c/', function ($id) {
        try {
            $rlde = Node::table('rlde')->find($id);
            $rlde->rule = "";
            $rlde->save();
            _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'rlde' . '/' . $id . '/');
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'rlde' . '/' . $id . '/');
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'rlde' . '/' . $id . '/');
        }
    });

    $app->get('/(\d+)/d/', function ($id) {
        try {
            $rlde = Node::table('rlde');

            if ($rlde->where('id', '=', $id)->findAll()->count() > 0) {
                $rlde->find($id)->delete();
                Node::table('stld')->where('rid', '=', $id)->delete();
                Node::table('clvr')->where('rid', '=', $id)->delete();
                Node::table('rrsr')->where('rid', '=', $id)->delete();
            }
            _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'rlde' . '/');
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'rlde' . '/');
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409), get_base_url() . 'rlde' . '/');
        }
    });
});

$app->setError(function () use($app) {

    $app->view->display('error/404', [
        'title' => '404 Error'
    ]);
});

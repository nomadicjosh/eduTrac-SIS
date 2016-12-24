<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * SQL Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$app->before('GET|POST', '/sql/', function() {
    if (!hasPermission('access_sql_interface_screen')) {
        _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->group('/sql', function() use ($app) {

    $app->match('GET|POST', '/', function() use($app) {
        if ($app->req->isPost()) {

            if (strstra(strtolower($_POST['qtext']), forbidden_keyword())) {
                _etsis_flash()->{'error'}(_t('Your query contains a forbidden keywork, please try again.'), $app->req->server['HTTP_REFERER']);
                exit();
            }

            try {
                $pdo = new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);

                if ($_POST['type'] == "query") {

                    $qtext2 = str_replace("\\", " ", str_replace("\\", "", $_POST['qtext']));
                    /* Write to activity log table. */
                    etsis_logger_activity_log_write("Query", "SQL Interface", $qtext2, get_persondata('uname'));

                    $result = $pdo->query("$qtext2");
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            }
        }

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('sql/index', [
            'title' => 'SQL Interface',
            'result' => $result
            ]
        );
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

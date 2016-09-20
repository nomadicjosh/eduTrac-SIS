<?php
/**
 * Config
 *  
 * @license GPLv3
 * 
 * @since       3.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
// Initial Installation Info!
$system = [];
$system['title'] = '{product}';
$system['release'] = '{release}';
$system['installed'] = '{datenow}';

defined('DB_HOST') or define('DB_HOST', '{hostname}');
defined('DB_NAME') or define('DB_NAME', '{database}');
defined('DB_USER') or define('DB_USER', '{username}');
defined('DB_PASS') or define('DB_PASS', '{password}');

$app->inst->singleton('db', function () {
    $pdo = new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->query('SET CHARACTER SET utf8');
    return new \Liten\Orm($pdo);
});
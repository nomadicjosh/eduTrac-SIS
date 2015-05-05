<?php
/**
 * Config
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
// Initial Installation Info!
$system = [];
$system['title'] = '{product}';
$system['company'] = '{company}';
$system['version'] = '{version}';
$system['installed'] = '{datenow}';

$subdomain = '';
$domain_parts = explode('.', $_SERVER['SERVER_NAME']);
if (count($domain_parts) == 3) {
    $subdomain = $domain_parts[0];

    if ($subdomain == 'www') {
        $subdomain = '';
    }
}

//defined( 'MULTICAMPUS' )                	or define( 'MULTICAMPUS', true );
/* Begin choose database based on subdomain and type of installation. */
if ($subdomain != '' && MULTICAMPUS === true) {
    defined('DB_HOST') or define('DB_HOST', '{hostname}');
    defined('DB_NAME') or define('DB_NAME', 'dbprefix_' . $subdomain);
    defined('DB_USER') or define('DB_USER', '{username}');
    defined('DB_PASS') or define('DB_PASS', '{password}');
} else {
    defined('DB_HOST') or define('DB_HOST', '{hostname}');
    defined('DB_NAME') or define('DB_NAME', '{database}');
    defined('DB_USER') or define('DB_USER', '{username}');
    defined('DB_PASS') or define('DB_PASS', '{password}');
}

$app->inst->singleton('db', function () {
    $pdo = new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    return new \Liten\Orm($pdo);
});
$orm = $app->inst->db;

$app->inst->singleton('hook', function () {
    return new \app\src\Hooks();
});

$app->inst->singleton('module', function () {
    return new \app\src\Modules();
});

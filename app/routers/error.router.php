<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Error Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app->get('404', function () use($app) {
    $app->view->display('error/404');
});

/**
 * Before route checks to make sure the logged in user
 * us allowed to manage options/settings.
 */
$app->before('GET', '/err/screen-error.*', function() use($app) {
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
    
    if(empty($app->req->server['HTTP_REFERER']) === true) {
        redirect(url('/dashboard/'));
    }
});

$app->get('/err/screen-error/', function () use($app) {
    $app->view->display('error/screen-error');
});

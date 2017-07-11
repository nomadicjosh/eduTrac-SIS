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
        etsis_redirect(get_base_url());
    }
    
    if(empty($app->req->server['HTTP_REFERER']) === true) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->get('/err/screen-error/', function () use($app) {
    $error = new \app\src\Core\Exception\Exception(
            sprintf(_t('The screen %s does not exist. Please try your search again.'), strtoupper(_h(_filter_input_string(INPUT_GET, 'code')))),
            'screen_error'
    );
    $app->view->display('error/screen-error', ['error' => $error]);
});

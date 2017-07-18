<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Before route check.
 */
$app->before('GET|POST', '/setting/', function() {
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        exit();
    }
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        exit();
    }
});


$app->match('GET|POST', '/setting/', function () use($app) {

    if ($app->req->isPost()) {
        $options = [
            'institution_name', 'cookieexpire', 'cookiepath', 'myetsis_layout', 'myetsis_offline_message',
            'enable_benchmark', 'edutrac_analytics_url', 'curl', 'api_key', 'help_desk', 'enable_cron_jobs',
            'contact_phone', 'mailing_address', 'enable_myetsis_portal', 'enable_myetsis_appl_form', 'screen_caching', 'db_caching',
            'system_timezone', 'etsis_core_locale', 'send_acceptance_email', 'elfinder_driver', 'amz_s3_bucket', 'amz_s3_access_key',
            'amz_s3_secret_key'
        ];

        foreach ($options as $option_name) {
            if (!isset($app->req->post[$option_name]))
                continue;
            $value = $app->req->post[$option_name];
            update_option($option_name, $value);
        }
        // Update more options here
        $app->hook->do_action('update_options');
        /* Write to logs */
        etsis_logger_activity_log_write('Update', 'Settings', 'System Settings', get_persondata('uname'));
        _etsis_flash()->success(_t('Settings saved successfully.'), $app->req->server['HTTP_REFERER']);
    }

    etsis_register_style('form');
    etsis_register_script('select');
    etsis_register_script('select2');

    $app->view->display('setting/index', [
        'title' => 'General Settings'
        ]
    );
});

/**
 * Before route check.
 */
$app->before('GET|POST', '/registration/', function() {
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        exit();
    }
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        exit();
    }
});

$app->match('GET|POST', '/registration/', function () use($app) {

    if ($app->req->isPost()) {
        $options = [
            'open_registration', 'current_term_code', 'number_of_courses',
            'account_balance', 'open_terms', 'registration_term',
            'reg_instructions', 'open_webreg_date'
        ];

        foreach ($options as $option_name) {
            if (!isset($app->req->post[$option_name]))
                continue;
            $value = $app->req->post[$option_name];
            update_option($option_name, $value);
        }
        // Update more options here
        $app->hook->do_action('update_options');
        /* Write to logs */
        etsis_logger_activity_log_write('Update', 'Settings', 'Registration Settings', get_persondata('uname'));
        _etsis_flash()->success(_t('Settings saved successfully.'), $app->req->server['HTTP_REFERER']);
    }

    etsis_register_style('form');
    etsis_register_script('select');
    etsis_register_script('select2');
    etsis_register_script('datepicker');

    $app->view->display('setting/registration', [
        'title' => 'Registration Settings'
        ]
    );
});

/**
 * Before route checks to make sure the logged in user
 * us allowed to manage options/settings.
 */
$app->before('GET|POST', '/email/', function() {
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        exit();
    }
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        exit();
    }
});

$app->match('GET|POST', '/email/', function () use($app) {

    if ($app->req->isPost()) {
        $options = [ 'system_email', 'contact_email', 'room_request_email', 'registrar_email_address', 'admissions_email'];

        foreach ($options as $option_name) {
            if (!isset($app->req->post[$option_name]))
                continue;
            $value = $app->req->post[$option_name];
            update_option($option_name, $value);
        }
        // Update more options here
        $app->hook->do_action('update_options');
        /* Write to logs */
        etsis_logger_activity_log_write('Update', 'Settings', 'Email Settings', get_persondata('uname'));
        _etsis_flash()->success(_t('Settings saved successfully.'), $app->req->server['HTTP_REFERER']);
    }

    etsis_register_style('form');
    etsis_register_script('select');
    etsis_register_script('select2');

    $app->view->display('setting/email', [
        'title' => 'Email Settings'
        ]
    );
});

/**
 * Before route checks to make sure the logged in user
 * us allowed to manage options/settings.
 */
$app->before('GET|POST', '/templates/', function() {
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        exit();
    }
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        exit();
    }
});

$app->match('GET|POST', '/templates/', function () use($app) {

    if ($app->req->isPost()) {
        $options = [
            'coa_form_text', 'reset_password_text', 'room_request_text', 'room_booking_confirmation_text',
            'student_acceptance_letter', 'person_login_details'
        ];

        foreach ($options as $option_name) {
            if (!isset($app->req->post[$option_name]))
                continue;
            $value = $app->req->post[$option_name];
            update_option($option_name, $value);
        }
        // Update more options here
        $app->hook->do_action('update_options');
        /* Write to logs */
        etsis_logger_activity_log_write('Update', 'Settings', 'Email Templates', get_persondata('uname'));
        _etsis_flash()->success(_t('Settings saved successfully.'), $app->req->server['HTTP_REFERER']);
    }

    etsis_register_style('form');
    etsis_register_style('table');
    etsis_register_script('select');
    etsis_register_script('select2');
    etsis_register_script('datepicker');

    $app->view->display('setting/templates', [
        'title' => 'Email Templates'
        ]
    );
});

$app->before('GET|POST', '/sms/', function() {
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        exit();
    }
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        exit();
    }
});

$app->match('GET|POST', '/sms/', function () use($app) {

    if ($app->req->isPost()) {
        $options = [ 'twilio_account_sid', 'twilio_auth_token', 'twilio_phone_number'];

        foreach ($options as $option_name) {
            if (!isset($app->req->post[$option_name]))
                continue;
            $value = $app->req->post[$option_name];
            update_option($option_name, $value);
        }
        // Update more options here
        $app->hook->do_action('update_twilio_options');
        /* Write to logs */
        etsis_logger_activity_log_write('Update', 'Settings', 'Twilio Settings', get_persondata('uname'));
        _etsis_flash()->success(_t('Settings saved successfully.'), $app->req->server['HTTP_REFERER']);
    }

    etsis_register_style('form');
    etsis_register_script('select');
    etsis_register_script('select2');

    $app->view->display('setting/sms', [
        'title' => 'Twilio Settings'
        ]
    );
});

$app->before('POST', '/sms/test/', function() {
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        exit();
    }
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        exit();
    }
});

$app->post('/sms/test/', function () use($app) {
    try {
        $client = new Twilio\Rest\Client(get_option('twilio_account_sid'), get_option('twilio_auth_token'));
        $client->messages->create(
            $app->req->post['to'], // Text this number
            [
            'from' => get_option('twilio_phone_number'), // From a valid Twilio number
            'body' => $app->req->post['message']
            ]
        );
        _etsis_flash()->success(_t('Text message has been sent.'), get_base_url() . 'sms' . '/');
    } catch (Twilio\Exceptions\RestException $ex) {
        \Cascade\Cascade::getLogger('error')->error($ex->getMessage());
        _etsis_flash()->error($ex->getMessage(), get_base_url() . 'sms' . '/');
    }
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

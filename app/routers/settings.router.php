<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Before route check.
 */
$app->before('GET', '/setting/', function() {
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});


$app->match('GET|POST', '/setting/', function () use($app) {

    if ($app->req->isPost()) {
        $options = [
            'institution_name', 'cookieexpire', 'cookiepath', 'myetsis_layout', 'myetsis_offline_message',
            'enable_benchmark', 'edutrac_analytics_url', 'curl', 'api_key', 'help_desk',
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
$app->before('GET', '/registration/', function() {
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->match('GET|POST', '/registration/', function () use($app) {

    if ($app->req->isPost()) {
        $options = [
            'open_registration', 'current_term_code', 'number_of_courses',
            'account_balance', 'open_terms', 'registration_term',
            'reg_instructions'
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
    }

    etsis_register_style('form');
    etsis_register_script('select');
    etsis_register_script('select2');

    $app->view->display('setting/registration', [
        'title' => 'Registration Settings'
        ]
    );
});

/**
 * Before route checks to make sure the logged in user
 * us allowed to manage options/settings.
 */
$app->before('GET', '/email/', function() {
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
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
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->match('GET|POST', '/templates/', function () use($app) {

    if ($app->req->isPost()) {
        $options = [
            'coa_form_text', 'reset_password_text', 'room_request_text', 'room_booking_confirmation_text',
            'student_acceptance_letter', 'person_login_details', 'update_username'
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
    }

    etsis_register_style('form');
    etsis_register_script('select');
    etsis_register_script('select2');

    $app->view->display('setting/sms', [
        'title' => 'Twilio Settings'
        ]
    );
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;

/**
 * Before route check.
 */
$app->before('GET', '/setting/', function() {
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});


$app->match('GET|POST', '/setting/', function () use($app) {
    $css = [ 'css/admin/module.admin.page.form_elements.min.css'];
    $js = [
        'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
        'components/modules/admin/forms/editors/wysihtml5/assets/lib/js/wysihtml5-0.3.0_rc2.min.js?v=v2.1.0',
        'components/modules/admin/forms/editors/wysihtml5/assets/lib/js/bootstrap-wysihtml5-0.0.2.js?v=v2.1.0',
        'components/modules/admin/forms/editors/wysihtml5/assets/custom/wysihtml5.init.js?v=v2.1.0'
    ];

    if ($app->req->isPost()) {
        try {
            $options = [
                'institution_name', 'cookieexpire', 'cookiepath', 'myet_layout', 'myet_offline_message',
                'enable_benchmark', 'edutrac_analytics_url', 'curl', 'api_key', 'help_desk',
                'contact_phone', 'mailing_address', 'enable_myet_portal', 'enable_myet_appl_form', 'screen_caching', 'db_caching',
                'system_timezone', 'et_core_locale', 'send_acceptance_email', 'elfinder_driver', 'amz_s3_bucket', 'amz_s3_access_key',
                'amz_s3_secret_key'
            ];

            foreach ($options as $option_name) {
                if (!isset($_POST[$option_name]))
                    continue;
                $value = $_POST[$option_name];
                update_option($option_name, $value);
            }
            // Update more options here
            $app->hook->do_action('update_options');
            /* Write to logs */
            etsis_logger_activity_log_write('Update', 'Settings', 'System Settings', get_persondata('uname'));
            _etsis_flash()->{'success'}(_t('General settings saved successfully.'), $app->req->server['HTTP_REFERER']);
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        }
    }

    $app->view->display('setting/index', [
        'title' => 'General Settings',
        'cssArray' => $css,
        'jsArray' => $js
        ]
    );
});

/**
 * Before route check.
 */
$app->before('GET', '/registration/', function() {
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->match('GET|POST', '/registration/', function () use($app) {
    $css = [ 'css/admin/module.admin.page.form_elements.min.css'];
    $js = [
        'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0'
    ];

    if ($app->req->isPost()) {
        try {
            $options = [
                'open_registration', 'current_term_code', 'number_of_courses',
                'account_balance', 'open_terms', 'registration_term',
                'reg_instructions'
            ];

            foreach ($options as $option_name) {
                if (!isset($_POST[$option_name]))
                    continue;
                $value = $_POST[$option_name];
                update_option($option_name, $value);
            }
            // Update more options here
            $app->hook->do_action('update_options');
            /* Write to logs */
            etsis_logger_activity_log_write('Update', 'Settings', 'Registration Settings', get_persondata('uname'));
            _etsis_flash()->{'success'}(_t('Registration settings saved successfully.'), $app->req->server['HTTP_REFERER']);
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        }
    }

    $app->view->display('setting/registration', [
        'title' => 'Registration Settings',
        'cssArray' => $css,
        'jsArray' => $js
        ]
    );
});

/**
 * Before route checks to make sure the logged in user
 * us allowed to manage options/settings.
 */
$app->before('GET', '/email/', function() {
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->match('GET|POST', '/email/', function () use($app) {
    $css = [ 'css/admin/module.admin.page.form_elements.min.css'];
    $js = [
        'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
        'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
        'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
        'components/modules/admin/forms/editors/wysihtml5/assets/lib/js/wysihtml5-0.3.0_rc2.min.js?v=v2.1.0',
        'components/modules/admin/forms/editors/wysihtml5/assets/lib/js/bootstrap-wysihtml5-0.0.2.js?v=v2.1.0',
        'components/modules/admin/forms/editors/wysihtml5/assets/custom/wysihtml5.init.js?v=v2.1.0'
    ];

    if ($app->req->isPost()) {
        try {
            $options = [ 'system_email', 'contact_email', 'room_request_email', 'registrar_email_address', 'admissions_email'];

            foreach ($options as $option_name) {
                if (!isset($_POST[$option_name]))
                    continue;
                $value = $_POST[$option_name];
                update_option($option_name, $value);
            }
            // Update more options here
            $app->hook->do_action('update_options');
            /* Write to logs */
            etsis_logger_activity_log_write('Update', 'Settings', 'Email Settings', get_persondata('uname'));
            _etsis_flash()->{'success'}(_t('Email settings saved successfully.'), $app->req->server['HTTP_REFERER']);
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        }
    }

    $app->view->display('setting/email', [
        'title' => 'Email Settings',
        'cssArray' => $css,
        'jsArray' => $js
        ]
    );
});

/**
 * Before route checks to make sure the logged in user
 * us allowed to manage options/settings.
 */
$app->before('GET|POST', '/templates/', function() {
    if (!hasPermission('edit_settings')) {
        _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
    }
});

$app->match('GET|POST', '/templates/', function () use($app) {

    if ($app->req->isPost()) {
        try {
            $options = [
                'coa_form_text', 'reset_password_text', 'room_request_text', 'room_booking_confirmation_text',
                'student_acceptance_letter', 'person_login_details', 'update_username'
            ];

            foreach ($options as $option_name) {
                if (!isset($_POST[$option_name]))
                    continue;
                $value = $_POST[$option_name];
                update_option($option_name, $value);
            }
            // Update more options here
            $app->hook->do_action('update_options');
            /* Write to logs */
            etsis_logger_activity_log_write('Update', 'Settings', 'Email Templates', get_persondata('uname'));
            _etsis_flash()->{'success'}(_t('Email Template settings saved successfully.'), $app->req->server['HTTP_REFERER']);
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        }
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
        try {
            $options = [ 'twilio_account_sid', 'twilio_auth_token', 'twilio_phone_number'];

            foreach ($options as $option_name) {
                if (!isset($_POST[$option_name]))
                    continue;
                $value = $_POST[$option_name];
                update_option($option_name, $value);
            }
            // Update more options here
            $app->hook->do_action('update_twilio_options');
            /* Write to logs */
            etsis_logger_activity_log_write('Update', 'Settings', 'Twilio Settings', get_persondata('uname'));
            _etsis_flash()->{'success'}(_t('Twilio settings saved successfully.'), $app->req->server['HTTP_REFERER']);
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
        }
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

<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

$logger = new \app\src\Log;

/**
 * Before route check.
 */
$app->before('GET', '/setting/', function() {
    if (!hasPermission('edit_settings')) {
        redirect(url('/dashboard/'));
    }

    /**
     * If user is logged in and the lockscreen cookie is set, 
     * redirect user to the lock screen until he/she enters 
     * his/her password to gain access.
     */
    if (isset($_COOKIE['SCREENLOCK'])) {
        redirect(url('/lock/'));
    }
});


$app->match('GET|POST', '/setting/', function () use($app, $logger) {
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
        $options = [ 'enable_ssl', 'institution_name', 'cookieexpire', 'cookiepath', 'myet_offline_message',
            'maintenance_mode', 'enable_benchmark', 'nav_collapse', 'enable_cron_jobs',
            'enable_cron_log', 'wwo_key', 'location', 'autodetect_location', 'edutrac_analytics_url',
            'autodetect_type', 'degree_units', 'wind_units', 'curl', 'auth_token', 'help_desk', 'reset_password_text',
            'contact_phone', 'mailing_address', 'enable_myet_portal', 'enable_myet_appl_form', 'screen_caching', 'db_caching',
            'system_timezone'
        ];

        foreach ($options as $option_name) {
            if (!isset($_POST[$option_name]))
                continue;
            $value = $_POST[$option_name];
            $app->hook->{'update_option'}($option_name, $value);
        }
        // Update more options here
        $app->hook->{'do_action'}('update_options');
        /* Write to logs */
        $logger->setLog('Update', 'Settings', 'System Settings', get_persondata('uname'));
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
        redirect(url('/dashboard/'));
    }

    /**
     * If user is logged in and the lockscreen cookie is set, 
     * redirect user to the lock screen until he/she enters 
     * his/her password to gain access.
     */
    if (isset($_COOKIE['SCREENLOCK'])) {
        redirect(url('/lock/'));
    }
});

$app->match('GET|POST', '/registration/', function () use($app, $logger) {
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
        $options = [
            'open_registration', 'current_term_code', 'number_of_courses',
            'account_balance', 'open_terms', 'registration_term',
            'reg_instructions'
        ];

        foreach ($options as $option_name) {
            if (!isset($_POST[$option_name]))
                continue;
            $value = $_POST[$option_name];
            $app->hook->{'update_option'}($option_name, $value);
        }
        // Update more options here
        $app->hook->{'do_action'}('update_options');
        /* Write to logs */
        $logger->setLog('Update', 'Settings', 'Registration Settings', get_persondata('uname'));
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
        redirect(url('/dashboard/'));
    }

    /**
     * If user is logged in and the lockscreen cookie is set, 
     * redirect user to the lock screen until he/she enters 
     * his/her password to gain access.
     */
    if (isset($_COOKIE['SCREENLOCK'])) {
        redirect(url('/lock/'));
    }
});

$app->match('GET|POST', '/email', function () use($app, $logger) {
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
        $options = [ 'system_email', 'contact_email', 'registrar_email_address', 'contact_email', 'admissions_email', 'coa_form_text'];

        foreach ($options as $option_name) {
            if (!isset($_POST[$option_name]))
                continue;
            $value = $_POST[$option_name];
            $app->hook->{'update_option'}($option_name, $value);
        }
        // Update more options here
        $app->hook->{'do_action'}('update_options');
        /* Write to logs */
        $logger->setLog('Update', 'Settings', 'Email Settings', get_persondata('uname'));
    }

    $app->view->display('setting/email', [
        'title' => 'Email Settings',
        'cssArray' => $css,
        'jsArray' => $js
        ]
    );
});

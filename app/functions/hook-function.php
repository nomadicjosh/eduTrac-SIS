<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

/**
 * eduTrac SIS Hooks Helper & Wrapper
 *
 * @license GPLv3
 *         
 * @since 3.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();

/**
 * Wrapper function for Hooks::register_admin_page() and
 * register's a plugin administration page.
 *
 * @see Hooks::register_admin_page()
 *
 * @since 6.1.07
 * @param string $slug
 *            Plugin's slug.
 * @param string $title
 *            Title that is show for the plugin's link.
 * @param string $function
 *            The function which prints the plugin's page.
 */
function register_admin_page($slug, $title, $function)
{
    $app = \Liten\Liten::getInstance();
    return $app->hook->register_admin_page($slug, $title, $function);
}

/**
 * Wrapper function for Hooks::activate_plugin() and
 * activates plugin based on $_GET['id'].
 *
 * @see Hooks::activate_plugin()
 *
 * @since 6.0.04
 * @param string $id
 *            ID of the plugin to be activated.
 * @return mixed Activates plugin if it exists.
 */
function activate_plugin($id)
{
    $app = \Liten\Liten::getInstance();
    return $app->hook->activate_plugin($id);
}

/**
 * Wrapper function for Hooks::deactivate_plugin() and
 * deactivates plugin based on $_GET['id'].
 *
 * @see Hooks::deactivate_plugin()
 *
 * @since 6.0.04
 * @param string $id
 *            ID of the plugin to be deactivated.
 * @return mixed Deactivates plugin if it exists and is active.
 */
function deactivate_plugin($id)
{
    $app = \Liten\Liten::getInstance();
    return $app->hook->deactivate_plugin($id);
}

/**
 * Wrapper function for Hooks::load_activated_plugins() and
 * loads all activated plugins for inclusion.
 *
 * @see Hooks::load_activated_plugins()
 *
 * @since 6.0.03
 * @param string $plugins_dir
 *            Loads plugins from specified folder
 * @return mixed
 */
function load_activated_plugins($plugins_dir = '')
{
    $app = \Liten\Liten::getInstance();
    return $app->hook->load_activated_plugins($plugins_dir);
}

/**
 * Wrapper function for Hooks::is_plugin_activated() and
 * checks if a particular plugin is activated
 *
 * @see Hooks::is_plugin_activated()
 *
 * @since 6.0.03
 * @param string $plugin
 *            Name of plugin file.
 * @return bool False if plugin is not activated and true if it is activated.
 */
function is_plugin_activated($plugin)
{
    $app = \Liten\Liten::getInstance();
    return $app->hook->is_plugin_activated($plugin);
}

/**
 * Wrapper function for Hooks::get_option() method and
 * reads an option from options_meta table.
 *
 * @see Hooks::get_option()
 *
 * @since 6.0.03
 * @param string $meta_key
 *            Name of the option to retrieve.
 * @param mixed $default
 *            The default value.
 * @return mixed Returns value of default if not found.
 */
function get_option($meta_key, $default = false)
{
    $app = \Liten\Liten::getInstance();
    return $app->hook->get_option($meta_key, $default);
}

/**
 * Wrapper function for Hooks::update_option() method and
 * updates (add if doesn't exist) an option to options_meta table.
 *
 * @see Hooks::update_option()
 *
 * @since 6.0.03
 * @param string $meta_key
 *            Name of the option to update/add.
 * @param mixed $newvalue
 *            The new value to update with or add.
 * @return bool False if not updated or true if updated.
 */
function update_option($meta_key, $newvalue)
{
    $app = \Liten\Liten::getInstance();
    return $app->hook->update_option($meta_key, $newvalue);
}

/**
 * Wrapper function for Hooks::add_option() method and
 * adds a new option to the options_meta table.
 *
 * @see Hooks::add_option()
 *
 * @since 6.0.03
 * @param string $name
 *            Name of the option to add.
 * @param mixed $value
 *            The option value.
 * @return bool False if not added or true if added.
 */
function add_option($name, $value = '')
{
    $app = \Liten\Liten::getInstance();
    return $app->hook->add_option($name, $value);
}

/**
 * Wrapper function for Hooks::delete_option() method and
 * deletes an option for the options_meta table.
 *
 * @see Hooks::delete_option()
 *
 * @since 6.0.03
 * @param string $name
 *            Name of the option to delete.
 * @return bool False if not deleted or true if deleted.
 */
function delete_option($name)
{
    $app = \Liten\Liten::getInstance();
    return $app->hook->delete_option($name);
}

/**
 * JSTree Sidebar Menu Include
 *
 * Includes the jstree sidebar menu on several screens.
 *
 * @since 6.1.00
 * @return mixed
 */
function jstree_sidebar_menu($screen, $crse = '', $sect = '', $nae = '', $staff = '', $spro = '', $prog = '')
{
    $app = \Liten\Liten::getInstance();

    $menu = APP_PATH . 'views/dashboard/menu.php';
    if (!$app->hook->has_filter('core_sidebar_menu')) {
        require ($menu);
    }
    return $app->hook->apply_filter('core_sidebar_menu', $menu, $screen, $crse, $sect, $nae, $staff, $spro, $prog);
}

/**
 * Core admin bar include.
 *
 * @since 6.2.0
 */
function core_admin_bar()
{
    $app = \Liten\Liten::getInstance();

    $filename = APP_PATH . 'views/dashboard/core-admin-bar.php';

    if (!is_readable($filename)) {
        __return_false();
    }

    if (!$app->hook->has_filter('core_admin_bar')) {
        include ($filename);
    }
    return $app->hook->apply_filter('core_admin_bar', $filename);
}

/**
 * Mark a function as deprecated and inform when it has been used.
 *
 * There is a hook deprecated_function_run that will be called that can be used
 * to get the backtrace up to what file and function called the deprecated
 * function.
 *
 * The current behavior is to trigger a user error if APP_ENV is DEV.
 *
 * This function is to be used in every function that is deprecated.
 *
 * @since 6.2.0
 *       
 * @param string $function_name
 *            The function that was called.
 * @param string $release
 *            The release of eduTrac SIS that deprecated the function.
 * @param string $replacement
 *            Optional. The function that should have been called. Default null.
 */
function _deprecated_function($function_name, $release, $replacement = null)
{
    $app = \Liten\Liten::getInstance();

    /**
     * Fires when a deprecated function is called.
     *
     * @since 6.2.0
     *       
     * @param string $function_name
     *            The function that was called.
     * @param string $replacement
     *            The function that should have been called.
     * @param string $release
     *            The release of eduTrac SIS that deprecated the function.
     */
    $app->hook->do_action('deprecated_function_run', $function_name, $replacement, $release);

    /**
     * Filter whether to trigger an error for deprecated functions.
     *
     * @since 6.2.0
     *       
     * @param bool $trigger
     *            Whether to trigger the error for deprecated functions. Default true.
     */
    if (APP_ENV == 'DEV' && $app->hook->apply_filter('deprecated_function_trigger_error', true)) {
        if (function_exists('_t')) {
            if (!is_null($replacement)) {
                _trigger_error(sprintf(_t('%1$s() is <strong>deprecated</strong> since release %2$s! Use %3$s() instead. <br />'), $function_name, $release, $replacement), E_USER_DEPRECATED);
            } else {
                _trigger_error(sprintf(_t('%1$s() is <strong>deprecated</strong> since release %2$s with no alternative available. <br />'), $function_name, $release), E_USER_DEPRECATED);
            }
        } else {
            if (!is_null($replacement)) {
                _trigger_error(sprintf('%1$s() is <strong>deprecated</strong> since release %2$s! Use %3$s() instead. <br />', $function_name, $release, $replacement), E_USER_DEPRECATED);
            } else {
                _trigger_error(sprintf('%1$s() is <strong>deprecated</strong> since release %2$s with no alternative available. <br />', $function_name, $release), E_USER_DEPRECATED);
            }
        }
    }
}

/**
 * Mark a class as deprecated and inform when it has been used.
 *
 * There is a hook deprecated_class_run that will be called that can be used
 * to get the backtrace up to what file, function/class called the deprecated
 * class.
 *
 * The current behavior is to trigger a user error if APP_ENV is DEV.
 *
 * This function is to be used in every class that is deprecated.
 *
 * @since 6.2.0
 *       
 * @param string $class_name
 *            The class that was called.
 * @param string $release
 *            The release of eduTrac SIS that deprecated the class.
 * @param string $replacement
 *            Optional. The class that should have been called. Default null.
 */
function _deprecated_class($class_name, $release, $replacement = null)
{
    $app = \Liten\Liten::getInstance();

    /**
     * Fires when a deprecated class is called.
     *
     * @since 6.2.0
     *       
     * @param string $class_name
     *            The class that was called.
     * @param string $replacement
     *            The class that should have been called.
     * @param string $release
     *            The release of eduTrac SIS that deprecated the class.
     */
    $app->hook->do_action('deprecated_class_run', $class_name, $replacement, $release);

    /**
     * Filter whether to trigger an error for deprecated classes.
     *
     * @since 6.2.0
     *       
     * @param bool $trigger
     *            Whether to trigger the error for deprecated classes. Default true.
     */
    if (APP_ENV == 'DEV' && $app->hook->apply_filter('deprecated_class_trigger_error', true)) {
        if (function_exists('_t')) {
            if (!is_null($replacement)) {
                _trigger_error(sprintf(_t('%1$s() is <strong>deprecated</strong> since release %2$s! Use %3$s instead. <br />'), $class_name, $release, $replacement), E_USER_DEPRECATED);
            } else {
                _trigger_error(sprintf(_t('%1$s() is <strong>deprecated</strong> since release %2$s with no alternative available. <br />'), $class_name, $release), E_USER_DEPRECATED);
            }
        } else {
            if (!is_null($replacement)) {
                _trigger_error(sprintf('%1$s() is <strong>deprecated</strong> since release %2$s! Use %3$s instead. <br />', $class_name, $release, $replacement), E_USER_DEPRECATED);
            } else {
                _trigger_error(sprintf('%1$s() is <strong>deprecated</strong> since release %2$s with no alternative available. <br />', $class_name, $release), E_USER_DEPRECATED);
            }
        }
    }
}

/**
 * Mark a class's method as deprecated and inform when it has been used.
 *
 * There is a hook deprecated_class_method_run that will be called that can be used
 * to get the backtrace up to what file, function/class called the deprecated
 * method.
 *
 * The current behavior is to trigger a user error if APP_ENV is DEV.
 *
 * This function is to be used in every class's method that is deprecated.
 *
 * @since 6.2.0
 *       
 * @param string $method_name
 *            The class method that was called.
 * @param string $release
 *            The release of eduTrac SIS that deprecated the class's method.
 * @param string $replacement
 *            Optional. The class method that should have been called. Default null.
 */
function _deprecated_class_method($method_name, $release, $replacement = null)
{
    $app = \Liten\Liten::getInstance();

    /**
     * Fires when a deprecated class method is called.
     *
     * @since 6.2.0
     *       
     * @param string $method_name
     *            The class's method that was called.
     * @param string $replacement
     *            The class method that should have been called.
     * @param string $release
     *            The release of eduTrac SIS that deprecated the class's method.
     */
    $app->hook->do_action('deprecated_class_method_run', $method_name, $replacement, $release);

    /**
     * Filter whether to trigger an error for deprecated class methods.
     *
     * @since 6.2.0
     *       
     * @param bool $trigger
     *            Whether to trigger the error for deprecated class methods. Default true.
     */
    if (APP_ENV == 'DEV' && $app->hook->apply_filter('deprecated_class_method_trigger_error', true)) {
        if (function_exists('_t')) {
            if (!is_null($replacement)) {
                _trigger_error(sprintf(_t('%1$s() is <strong>deprecated</strong> since release %2$s! Use %3$s() instead. <br />'), $method_name, $release, $replacement), E_USER_DEPRECATED);
            } else {
                _trigger_error(sprintf(_t('%1$s() is <strong>deprecated</strong> since release %2$s with no alternative available. <br />'), $method_name, $release), E_USER_DEPRECATED);
            }
        } else {
            if (!is_null($replacement)) {
                _trigger_error(sprintf('%1$s() is <strong>deprecated</strong> since release %2$s! Use %3$s() instead. <br />', $method_name, $release, $replacement), E_USER_DEPRECATED);
            } else {
                _trigger_error(sprintf('%1$s() is <strong>deprecated</strong> since release %2$s with no alternative available. <br />', $method_name, $release), E_USER_DEPRECATED);
            }
        }
    }
}

/**
 * Mark a function argument as deprecated and inform when it has been used.
 *
 * This function is to be used whenever a deprecated function argument is used.
 * Before this function is called, the argument must be checked for whether it was
 * used by comparing it to its default value or evaluating whether it is empty.
 * For example:
 *
 * if ( ! empty( $deprecated ) ) {
 * _deprecated_argument( __FUNCTION__, '6.1.00' );
 * }
 *
 *
 * There is a hook deprecated_argument_run that will be called that can be used
 * to get the backtrace up to what file and function used the deprecated
 * argument.
 *
 * The current behavior is to trigger a user error if APP_ENV is set to DEV.
 *
 * @since 6.2.0
 *       
 * @param string $function_name
 *            The function that was called.
 * @param string $release
 *            The release of eduTrac SIS that deprecated the argument used.
 * @param string $message
 *            Optional. A message regarding the change. Default null.
 */
function _deprecated_argument($function_name, $release, $message = null)
{
    $app = \Liten\Liten::getInstance();

    /**
     * Fires when a deprecated argument is called.
     *
     * @since 6.2.0
     *       
     * @param string $function_name
     *            The function that was called.
     * @param string $message
     *            A message regarding the change.
     * @param string $release
     *            The release of eduTrac SIS that deprecated the argument used.
     */
    $app->hook->do_action('deprecated_argument_run', $function_name, $message, $release);
    /**
     * Filter whether to trigger an error for deprecated arguments.
     *
     * @since 3.0.0
     *       
     * @param bool $trigger
     *            Whether to trigger the error for deprecated arguments. Default true.
     */
    if (APP_ENV == 'DEV' && $app->hook->apply_filter('deprecated_argument_trigger_error', true)) {
        if (function_exists('_t')) {
            if (!is_null($message)) {
                _trigger_error(sprintf(_t('%1$s() was called with an argument that is <strong>deprecated</strong> since release %2$s! %3$s. <br />'), $function_name, $release, $message), E_USER_DEPRECATED);
            } else {
                _trigger_error(sprintf(_t('%1$s() was called with an argument that is <strong>deprecated</strong> since release %2$s with no alternative available. <br />'), $function_name, $release), E_USER_DEPRECATED);
            }
        } else {
            if (!is_null($message)) {
                _trigger_error(sprintf('%1$s() was called with an argument that is <strong>deprecated</strong> since release %2$s! %3$s. <br />', $function_name, $release, $message), E_USER_DEPRECATED);
            } else {
                _trigger_error(sprintf('%1$s() was called with an argument that is <strong>deprecated</strong> since release %2$s with no alternative available. <br />', $function_name, $release), E_USER_DEPRECATED);
            }
        }
    }
}

/**
 * Marks a deprecated action or filter hook as deprecated and throws a notice.
 *
 * Default behavior is to trigger a user error if `APP_ENV` is set to DEV.
 *
 * This function is called by the hook::do_action_deprecated() and Hook::apply_filter_deprecated()
 * functions, and so generally does not need to be called directly.
 *
 * @since 6.3.0
 * 
 * @param string $hook        The hook that was used.
 * @param string $release     The release of eduTrac SIS that deprecated the hook.
 * @param string $replacement Optional. The hook that should have been used.
 * @param string $message     Optional. A message regarding the change.
 */
function _deprecated_hook($hook, $release, $replacement = null, $message = null)
{

    $app = \Liten\Liten::getInstance();

    /**
     * Fires when a deprecated hook is called.
     *
     * @since 6.3.0
     * 
     * @param string $hook        The hook that was called.
     * @param string $replacement The hook that should be used as a replacement.
     * @param string $release     The release of eduTrac SIS that deprecated the argument used.
     * @param string $message     A message regarding the change.
     */
    $app->hook->do_action('deprecated_hook_run', $hook, $replacement, $release, $message);

    /**
     * Filters whether to trigger deprecated hook errors.
     *
     * @since 6.3.0
     * 
     * @param bool $trigger Whether to trigger deprecated hook errors. Requires
     *                      `APP_DEV` to be defined DEV.
     */
    if (APP_ENV == 'DEV' && $app->hook->apply_filter('deprecated_hook_trigger_error', true)) {
        $message = empty($message) ? '' : ' ' . $message;
        if (!is_null($replacement)) {
            _trigger_error(sprintf(__('%1$s is <strong>deprecated</strong> since release %2$s! Use %3$s instead.'), $hook, $release, $replacement) . $message, E_USER_DEPRECATED);
        } else {
            _trigger_error(sprintf(__('%1$s is <strong>deprecated</strong> since release %2$s with no alternative available.'), $hook, $release) . $message, E_USER_DEPRECATED);
        }
    }
}

/**
 * Mark something as being incorrectly called.
 *
 * There is a hook incorrectly_called_run that will be called that can be used
 * to get the backtrace up to what file and function called the deprecated
 * function.
 *
 * The current behavior is to trigger a user error if APP_ENV is set to DEV.
 *
 * @since 6.2.0
 *       
 * @param string $function_name
 *            The function that was called.
 * @param string $message
 *            A message explaining what has been done incorrectly.
 * @param string $release
 *            The release of eduTrac SIS where the message was added.
 */
function _incorrectly_called($function_name, $message, $release)
{
    $app = \Liten\Liten::getInstance();

    /**
     * Fires when the given function is being used incorrectly.
     *
     * @since 6.2.0
     *       
     * @param string $function_name
     *            The function that was called.
     * @param string $message
     *            A message explaining what has been done incorrectly.
     * @param string $release
     *            The release of eduTrac SIS where the message was added.
     */
    $app->hook->do_action('incorrectly_called_run', $function_name, $message, $release);

    /**
     * Filter whether to trigger an error for _incorrectly_called() calls.
     *
     * @since 3.1.0
     *       
     * @param bool $trigger
     *            Whether to trigger the error for _incorrectly_called() calls. Default true.
     */
    if (APP_ENV == 'DEV' && $app->hook->apply_filter('incorrectly_called_trigger_error', true)) {
        if (function_exists('_t')) {
            $release = is_null($release) ? '' : sprintf(_t('(This message was added in release %s.) <br /><br />'), $release);
            /* translators: %s: Codex URL */
            $message .= ' ' . sprintf(_t('Please see <a href="%s">Debugging in eduTrac SIS</a> for more information.'), 'https://developer.edutracsis.com/codex/debugging-edutrac-sis/');
            _trigger_error(sprintf(_t('%1$s() was called <strong>incorrectly</strong>. %2$s %3$s <br />'), $function_name, $message, $release));
        } else {
            $release = is_null($release) ? '' : sprintf('(This message was added in release %s.) <br /><br />', $release);
            $message .= sprintf(' Please see <a href="%s">Debugging in eduTrac SIS</a> for more information.', 'https://developer.edutracsis.com/codex/debugging-edutrac-sis/');
            _trigger_error(sprintf('%1$s() was called <strong>incorrectly</strong>. %2$s %3$s <br />', $function_name, $message, $release));
        }
    }
}

/**
 * Prints copyright in the dashboard footer.
 *
 * @since 6.2.0
 */
function etsis_dashboard_copyright_footer()
{
    $app = \Liten\Liten::getInstance();

    $copyright = '<!--  Copyright Line -->' . "\n";
    $copyright .= '<div class="copy">' . _t('&copy; 2013') . ' - ' . foot_release() . ' &nbsp; <a href="http://www.litenframework.com/"><img src="' . get_base_url() . 'static/assets/images/button.png" alt="Built with Liten Framework"/></a></div>' . "\n";
    $copyright .= '<!--  End Copyright Line -->' . "\n";

    return $app->hook->apply_filter('dashboard_copyright_footer', $copyright);
}
/**
 * Includes and loads all activated plugins.
 *
 * @since 1.0.0
 */
load_activated_plugins(APP_PATH . 'plugins' . DS);

/**
 * Includes and loads all available modules.
 *
 * @since 5.0.0
 */
$app->module->load_installed_modules(APP_PATH . 'modules' . DS);

/**
 * An action called to add the plugin's link
 * to the menu structure.
 *
 * @since 1.0.0
 * @uses $app->hook->do_action() Calls 'admin_menu' hook.
 */
$app->hook->do_action('admin_menu');

/**
 * An action called to add custom page links
 * to menu structure.
 *
 * @since 4.2.0
 * @uses $app->hook->do_action() Calls 'custom_plugin_page' hook.
 */
$app->hook->do_action('custom_plugin_page');

/**
 * Fires once activated plugins have loaded.
 *
 * @since 6.2.0
 */
$app->hook->do_action('plugin_loaded');

/**
 * Fires after eduTrac SIS has finished loading but before any headers are sent.
 *
 * @since 1.0.0
 */
$app->hook->do_action('init');

/**
 * Fires the admin_head action.
 *
 * @since 1.0.0
 */
function admin_head()
{
    $app = \Liten\Liten::getInstance();
    /**
     * Prints scripts and/or data in the head tag of the dashboard.
     *
     * @since 1.0.0
     */
    $app->hook->do_action('admin_head');
}

/**
 * Fires the myetsis_head action.
 *
 * @since 1.0.0
 */
function myetsis_head()
{
    $app = \Liten\Liten::getInstance();
    /**
     * Prints scripts and/or data in the head tag of the myetSIS self service
     * portal.
     *
     * @since 1.0.0
     */
    $app->hook->do_action('myetsis_head');
}

/**
 * Fires the footer action via the dashboard.
 *
 * @since 1.0.0
 */
function footer()
{
    $app = \Liten\Liten::getInstance();
    /**
     * Prints scripts and/or data before the ending body tag
     * of the dashboard.
     *
     * @since 1.0.0
     */
    $app->hook->do_action('footer');
}

/**
 * Fires the footer action via
 * myetSIS self service portal.
 *
 * @since 6.1.12
 */
function myetsis_footer()
{
    $app = \Liten\Liten::getInstance();
    /**
     * Prints scripts and/or data before the ending body tag of the myetSIS
     * self service portal.
     *
     * @since 6.1.12
     */
    $app->hook->do_action('myetsis_footer');
}

/**
 * Fires the etsis_dashboard_head action.
 *
 * @since 6.3.0
 */
function etsis_dashboard_head()
{
    $app = \Liten\Liten::getInstance();
    /**
     * Prints scripts and/or data in the head tag of the dashboard.
     *
     * @since 6.3.0
     */
    $app->hook->do_action('etsis_dashboard_head');
}

/**
 * Fires the etsis_dashboard_footer action via the dashboard.
 *
 * @since 6.3.0
 */
function etsis_dashboard_footer()
{
    $app = \Liten\Liten::getInstance();
    /**
     * Prints scripts and/or data before the ending body tag
     * of the dashboard.
     *
     * @since 6.3.0
     */
    $app->hook->do_action('etsis_dashboard_footer');
}

/**
 * Fires the release action.
 *
 * @since 1.0.0
 */
function release()
{
    $app = \Liten\Liten::getInstance();
    /**
     * Prints eduTrac SIS release information.
     *
     * @since 1.0.0
     */
    $app->hook->do_action('release');
}

/**
 * Fires the dashboard_top_widgets action.
 *
 * @since 1.0.0
 */
function dashboard_top_widgets()
{
    $app = \Liten\Liten::getInstance();
    /**
     * Prints widgets at the top portion of the dashboard.
     *
     * @since 1.0.0
     */
    $app->hook->do_action('dashboard_top_widgets');
}

/**
 * Shows number of active students.
 *
 * @since 4.0.0
 */
function dashboard_student_count()
{
    $app = \Liten\Liten::getInstance();
    try {
        $stu = $app->db->student()
            ->select('COUNT(student.stuID) as count')
            ->_join('sacp', 'student.stuID = sacp.stuID')
            ->where('student.status = "A"')->_and_()
            ->where('sacp.currStatus = "A"');
        $q = $stu->find(function ($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $a = [];
        foreach ($q as $r) {
            $a[] = $r;
        }
        $stuCount = '<div class="col-md-4">';
        $stuCount .= '<a href="#" class="widget-stats widget-stats-1 widget-stats-inverse">';
        $stuCount .= '<span class="glyphicons group"><i></i><span class="txt">' . _t('Active Students') . '</span></span>';
        $stuCount .= '<div class="clearfix"></div>';
        $stuCount .= '<span class="count">' . $r['count'] . '</span>';
        $stuCount .= '</a>';
        $stuCount .= '</div>';
        echo $app->hook->apply_filter('dashboard_student_count', $stuCount);
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
}

/**
 * Shows number of active courses.
 *
 * @since 4.0.0
 */
function dashboard_course_count()
{
    $app = \Liten\Liten::getInstance();
    try {
        $count = $app->db->course()
            ->where('course.currStatus = "A"')->_and_()
            ->where('course.endDate IS NULL')->_or_()
            ->whereLte('course.endDate','0000-00-00')
            ->count('course.courseID');

        $crseCount = '<div class="col-md-4">';
        $crseCount .= '<a href="#" class="widget-stats widget-stats-1 widget-stats-inverse">';
        $crseCount .= '<span class="glyphicons book"><i></i><span class="txt">' . _t('Active Courses') . '</span></span>';
        $crseCount .= '<div class="clearfix"></div>';
        $crseCount .= '<span class="count">' . $count . '</span>';
        $crseCount .= '</a>';
        $crseCount .= '</div>';
        echo $app->hook->apply_filter('dashboard_course_count', $crseCount);
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
}

/**
 * Shows number of active academic programs.
 *
 * @since 4.0.0
 */
function dashboard_acadProg_count()
{
    $app = \Liten\Liten::getInstance();
    try {
        $count = $app->db->acad_program()
            ->where('acad_program.currStatus = "A"')->_and_()
            ->where('acad_program.endDate IS NULL')->_or_()
            ->whereLte('acad_program.endDate','0000-00-00')
            ->count('acad_program.id');

        $progCount = '<div class="col-md-4">';
        $progCount .= '<a href="#" class="widget-stats widget-stats-1 widget-stats-inverse">';
        $progCount .= '<span class="glyphicons keynote"><i></i><span class="txt">' . _t('Active Programs') . '</span></span>';
        $progCount .= '<div class="clearfix"></div>';
        $progCount .= '<span class="count">' . $count . '</span>';
        $progCount .= '</a>';
        $progCount .= '</div>';
        echo $app->hook->apply_filter('dashboard_acadProg_count', $progCount);
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
}

/**
 * Shows update message when a new release of
 * eduTrac SIS is available.
 *
 * @since 4.0.0
 */
function show_update_message()
{
    /* $app = \Liten\Liten::getInstance();
      $acl = new \app\src\ACL(get_persondata('personID'));
      if ($acl->userHasRole(8)) {
      $update = new \VisualAppeal\AutoUpdate(rtrim($app->config('file.savepath'), '/'), BASE_PATH, 1800);
      $update->setCurrentVersion(RELEASE_TAG);
      $update->setUpdateUrl('https://etsis.s3.amazonaws.com/core/1.1/update-check');

      // Optional:
      $update->addLogHandler(new Monolog\Handler\StreamHandler(APP_PATH . 'tmp/logs/core-update.' . \Jenssegers\Date\Date::now()->format('m-d-Y') . '.txt'));
      $update->setCache(new Desarrolla2\Cache\Adapter\File(APP_PATH . 'tmp/cache'), 3600);
      if ($update->checkUpdate() !== false) {
      if ($update->newVersionAvailable()) {
      $alert = '<div class="alerts alerts-warn center">';
      $alert .= sprintf(_t('eduTrac SIS release %s is available for download/upgrade. Before upgrading, make sure to <a href="%s">backup your system</a>.'), $update->getLatestVersion(), 'https://www.edutracsis.com/manual/edutrac-sis-backups/');
      $alert .= '</div>';
      }
      }
      }
      return $app->hook->apply_filter('update_message', $alert); */
}

/**
 * Retrieve javascript directory uri.
 *
 * @since 4.1.9
 * @uses $app->hook->apply_filter() Calls 'javascript_directory_uri' filter.
 *      
 * @return string eduTrac javascript url.
 */
function get_javascript_directory_uri()
{
    $app = \Liten\Liten::getInstance();

    $directory = 'static/assets/components';
    $javascript_root_uri = get_base_url();
    $javascript_dir_uri = "$javascript_root_uri$directory/";
    return $app->hook->apply_filter('javascript_directory_uri', $javascript_dir_uri, $javascript_root_uri, $directory);
}

/**
 * Retrieve less directory uri.
 *
 * @since 4.1.9
 * @uses $app->hook->apply_filter() Calls 'less_directory_uri' filter.
 *      
 * @return string eduTrac less url.
 */
function get_less_directory_uri()
{
    $app = \Liten\Liten::getInstance();

    $directory = 'static/assets/less';
    $less_root_uri = get_base_url();
    $less_dir_uri = "$less_root_uri$directory/";
    return $app->hook->apply_filter('less_directory_uri', $less_dir_uri, $less_root_uri, $directory);
}

/**
 * Retrieve css directory uri.
 *
 * @since 4.1.9
 * @uses $app->hook->apply_filter() Calls 'css_directory_uri' filter.
 *      
 * @return string eduTrac css url.
 */
function get_css_directory_uri()
{
    $app = \Liten\Liten::getInstance();

    $directory = 'static/assets/css';
    $css_root_uri = get_base_url();
    $css_dir_uri = "$css_root_uri$directory/";
    return $app->hook->apply_filter('css_directory_uri', $css_dir_uri, $css_root_uri, $directory);
}

/**
 * Parses a string into variables to be stored in an array.
 *
 * Uses {@link http://www.php.net/parse_str parse_str()}
 *
 * @since 6.2.0
 * @param string $string
 *            The string to be parsed.
 * @param array $array
 *            Variables will be stored in this array.
 */
function etsis_parse_str($string, $array)
{
    $app = \Liten\Liten::getInstance();

    parse_str($string, $array);
    /**
     * Filter the array of variables derived from a parsed string.
     *
     * @since 4.2.0
     * @param array $array
     *            The array populated with variables.
     */
    $array = $app->hook->apply_filter('etsis_parse_str', $array);
}

/**
 * Frontend portal site title.
 *
 * @since 4.3
 * @uses $app->hook->apply_filter() Calls 'met_title' filter.
 *      
 * @return string eduTrac SIS frontend site title.
 */
function get_met_title()
{
    $app = \Liten\Liten::getInstance();

    $title = '<em>' . _t('my') . '</em>' . ('etSIS');
    return $app->hook->apply_filter('met_title', $title);
}

/**
 * myetSIS welcome message title.
 *
 * @since 6.2.2
 * @uses $app->hook->apply_filter() Calls 'met_welcome_message_title' filter.
 *      
 * @return string eduTrac SIS welcome message title.
 */
function get_met_welcome_message_title()
{
    $app = \Liten\Liten::getInstance();

    $title = _t('Welcome to myetSIS');
    return $app->hook->apply_filter('met_welcome_message_title', $title);
}

/**
 * Frontend portal footer powered by and release.
 *
 * @since 4.3
 * @uses $app->hook->apply_filter() Calls 'met_footer_release' filter.
 *      
 * @return mixed.
 */
function get_met_footer_release()
{
    $app = \Liten\Liten::getInstance();

    if (CURRENT_RELEASE != RELEASE_TAG) {
        $release = _t('Powered by eduTrac SIS r') . CURRENT_RELEASE . ' (t' . RELEASE_TAG . ')';
    } else {
        $release = _t('Powered by eduTrac SIS r') . CURRENT_RELEASE;
    }
    return $app->hook->apply_filter('met_footer_release', $release);
}

/**
 * Frontend portal footer title.
 *
 * @since 4.3
 * @uses $app->hook->apply_filter() Calls 'met_footer_title' filter.
 *      
 * @return string
 */
function get_met_footer_title()
{
    $app = \Liten\Liten::getInstance();

    $title = '<em>' . _t('my') . '</em>' . ('eduTrac');
    return $app->hook->apply_filter('met_footer_title', $title);
}

/**
 * Address type select: shows general list of address types and
 * if $typeCode is not NULL, shows the address type attached
 * to a particular record.
 *
 * @since 1.0.0
 * @param string $typeCode            
 * @return string Returns the record key if selected is true.
 */
function address_type_select($typeCode = NULL)
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="addressType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
            <option value="">&nbsp;</option>
            <option value="B"' . selected($typeCode, 'B', false) . '>Business</option>
            <option value="H"' . selected($typeCode, 'H', false) . '>Home/Mailing</option>
            <option value="P"' . selected($typeCode, 'P', false) . '>Permanent</option>
            </select>';
    return $app->hook->apply_filter('address_type', $select, $typeCode);
}

/**
 * Department Type select: shows general list of department types and
 * if $typeCode is not NULL, shows the department type attached
 * to a particular record.
 *
 * @since 1.0.0
 * @param string $typeCode            
 * @return string Returns the record key if selected is true.
 */
function dept_type_select($typeCode = NULL)
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="deptTypeCode" id="deptType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
            <option value="">&nbsp;</option>
            <option value="ADMIN"' . selected($typeCode, 'ADMIN', false) . '>' . _t('Administrative') . '</option>
            <option value="ACAD"' . selected($typeCode, 'ACAD', false) . '>' . _t('Academic') . '</option>
            </select>';
    return $app->hook->apply_filter('dept_type', $select, $typeCode);
}

/**
 * Acad Level select: shows general list of academic levels and
 * if $status is not NULL, shows the academic level attached
 * to a particular record.
 *
 * @since 1.0.0
 * @param string $status            
 * @return string Returns the record status if selected is true.
 */
function address_status_select($status = NULL)
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="addressStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
			<option value="">&nbsp;</option>
	    	<option value="C"' . selected($status, 'C', false) . '>Current</option>
			<option value="I"' . selected($status, 'I', false) . '>Inactive</option>
		    </select>';
    return $app->hook->apply_filter('address_status', $select, $status);
}

/**
 * Status dropdown: shows general list of statuses and
 * if $status is not NULL, shows the current status
 * for a particular record.
 *
 * @since 1.0.0
 * @param string $status            
 * @return string Returns the record key if selected is true.
 */
function status_select($status = NULL, $readonly = '')
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="currStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"' . $readonly . ' required>
    			<option value="">&nbsp;</option>
    	    	<option value="A"' . selected($status, 'A', false) . '>A Active</option>
    	    	<option value="I"' . selected($status, 'I', false) . '>I Inactive</option>
    			<option value="P"' . selected($status, 'P', false) . '>P Pending</option>
    			<option value="O"' . selected($status, 'O', false) . '>O Obsolete</option>
		        </select>';
    return $app->hook->apply_filter('general_status', $select, $status);
}

/**
 * Course section select: shows general list of statuses and
 * if $status is not NULL, shows the current status
 * for a particular course section record.
 *
 * @since 1.0.0
 * @param string $status            
 * @return string Returns the record key if selected is true.
 */
function course_sec_status_select($status = NULL, $readonly = '')
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="currStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required' . $readonly . '>
    			<option value="">&nbsp;</option>
    	    	<option' . dopt('activate_course_sec') . ' value="A"' . selected($status, 'A', false) . '>A Active</option>
    	    	<option value="I"' . selected($status, 'I', false) . '>I Inactive</option>
    			<option value="P"' . selected($status, 'P', false) . '>P Pending</option>
    			<option' . dopt('cancel_course_sec') . ' value="C"' . selected($status, 'C', false) . '>C Cancel</option>
    			<option value="O"' . selected($status, 'O', false) . '>O Obsolete</option>
		        </select>';
    return $app->hook->apply_filter('course_sec_status', $select, $status);
}

/**
 * Person type select: shows general list of person types and
 * if $type is not NULL, shows the person type
 * for a particular person record.
 *
 * @since 1.0.0
 * @param string $type            
 * @return string Returns the record type if selected is true.
 */
function person_type_select($type = NULL)
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="personType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="FAC"' . selected($type, 'FAC', false) . '>FAC Faculty</option>
                <option value="ADJ"' . selected($type, 'ADJ', false) . '>ADJ Adjunct</option>
                <option value="STA"' . selected($type, 'STA', false) . '>STA Staff</option>
                <option value="APL"' . selected($type, 'APL', false) . '>APL Applicant</option>
                <option value="STU"' . selected($type, 'STU', false) . '>STU Student</option>
                </select>';
    return $app->hook->apply_filter('person_type', $select, $type);
}

/**
 * Instructor method select: shows general list of instructor methods and
 * if $method is not NULL, shows the instructor method
 * for a particular course section.
 *
 * @since 1.0.0
 * @param string $method            
 * @return string Returns the record method if selected is true.
 */
function instructor_method($method = NULL)
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="instructorMethod" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="LEC"' . selected($method, 'LEC', false) . '>' . _t('LEC Lecture') . '</option>
                <option value="LAB"' . selected($method, 'LAB', false) . '>' . _t('LAB Lab') . '</option>
                <option value="SEM"' . selected($method, 'SEM', false) . '>' . _t('SEM Seminar') . '</option>
                <option value="LL"' . selected($method, 'LL', false) . '>' . _t('LL Lecture + Lab') . '</option>
                <option value="LS"' . selected($method, 'LS', false) . '>' . _t('LS Lecture + Seminar') . '</option>
                <option value="SL"' . selected($method, 'SL', false) . '>' . _t('SL Seminar + Lab') . '</option>
                <option value="LLS"' . selected($method, 'LLS', false) . '>' . _t('LLS Lecture + Lab + Seminar') . '</option>
                </select>';
    return $app->hook->apply_filter('instructor_method', $select, $method);
}

/**
 * Student Course section status select: shows general list of course sec statuses and
 * if $status is not NULL, shows the status
 * for a particular student course section record.
 *
 * @since 6.3.0
 * @param string $status            
 * @return string Returns the record status if selected is true.
 */
function stcs_status_select($status = NULL, $readonly = '')
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required' . $readonly . '>
                <option value="">&nbsp;</option>
                <option value="A"' . selected($status, 'A', false) . '>' . _t('A Add') . '</option>
                <option value="N"' . selected($status, 'N', false) . '>' . _t('N New') . '</option>
                <option value="D"' . selected($status, 'D', false) . '>' . _t('D Drop') . '</option>
                <option value="W"' . selected($status, 'W', false) . '>' . _t('W Withdrawn') . '</option>
                <option value="C"' . selected($status, 'C', false) . '>' . _t('C Cancelled') . '</option>
                </select>';
    return $app->hook->apply_filter('stcs_status', $select, $status);
}

/**
 * Student program status select: shows general list of student
 * statuses and if $status is not NULL, shows the status
 * for a particular student program record.
 *
 * @since 6.3.0
 * @param string $status            
 * @return string Returns the record status if selected is true.
 */
function sacp_status_select($status = NULL)
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="currStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="A"' . selected($status, 'A', false) . '>' . _t('A Active') . '</option>
                <option value="P"' . selected($status, 'P', false) . '>' . _t('P Potential') . '</option>
                <option value="W"' . selected($status, 'W', false) . '>' . _t('W Withdrawn') . '</option>
                <option value="C"' . selected($status, 'C', false) . '>' . _t('C Changed Mind') . '</option>
                <option value="G"' . selected($status, 'G', false) . '>' . _t('G Graduated') . '</option>
                </select>';
    return $app->hook->apply_filter('sacp_status', $select, $status);
}

/**
 * Credit type select: shows general list of credit types and
 * if $status is not NULL, shows the credit type
 * for a particular course or course section record.
 *
 * @since 1.0.0
 * @param string $status            
 * @return string Returns the record type if selected is true.
 */
function credit_type($status = NULL)
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="I"' . selected($status, 'I', false) . '>' . _t('I Institutional') . '</option>
                <option value="TR"' . selected($status, 'TR', false) . '>' . _t('TR Transfer') . '</option>
                <option value="AP"' . selected($status, 'AP', false) . '>' . _t('AP Advanced Placement') . '</option>
                <option value="X"' . selected($status, 'X', false) . '>' . _t('X Exempt') . '</option>
                <option value="T"' . selected($status, 'T', false) . '>' . _t('T Test') . '</option>
                </select>';
    return $app->hook->apply_filter('credit_type', $select, $status);
}

/**
 * Class year select: shows general list of class years and
 * if $year is not NULL, shows the class year
 * for a particular student.
 *
 * @since 1.0.0
 * @param string $year            
 * @return string Returns the record year if selected is true.
 */
function class_year($year = NULL)
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="classYear" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                <option value="">&nbsp;</option>
                <option value="FR"' . selected($year, 'FR', false) . '>' . _t('FR Freshman') . '</option>
                <option value="SO"' . selected($year, 'SO', false) . '>' . _t('SO Sophomore') . '</option>
                <option value="JR"' . selected($year, 'JR', false) . '>' . _t('JR Junior') . '</option>
                <option value="SR"' . selected($year, 'SR', false) . '>' . _t('SR Senior') . '</option>
                <option value="UG"' . selected($year, 'UG', false) . '>' . _t('UG Undergraduate Student') . '</option>
                <option value="GR"' . selected($year, 'GR', false) . '>' . _t('GR Grad Student') . '</option>
                <option value="PhD"' . selected($year, 'PhD', false) . '>' . _t('PhD PhD Student') . '</option>
                </select>';
    return $app->hook->apply_filter('class_year', $select, $year);
}

/**
 * Grading scale: shows general list of letter grades and
 * if $grade is not NULL, shows the grade
 * for a particular student course section record
 *
 * @since 1.0.0
 * @param string $grade            
 * @return string Returns the stcs grade if selected is true.
 */
function grading_scale($grade = NULL)
{
    $app = \Liten\Liten::getInstance();
    try {
        $select = '<select name="grade[]" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>' . "\n";
        $select .= '<option value="">&nbsp;</option>' . "\n";
        $scale = $app->db->query('SELECT * FROM grade_scale WHERE status = "1"');
        $q = $scale->find(function ($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        foreach ($q as $r) {
            $select .= '<option value="' . _h($r['grade']) . '"' . selected($grade, _h($r['grade']), false) . '>' . _h($r['grade']) . '</option>' . "\n";
        }
        $select .= '</select>';
        return $app->hook->apply_filter('grading_scale', $select, $grade);
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
}

function grades($id, $aID)
{
    $app = \Liten\Liten::getInstance();
    try {
        $grade = $app->db->query('SELECT * FROM gradebook WHERE stuID = ? AND assignID = ?', [
            $id,
            $aID
        ]);
        $q = $grade->find(function ($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        $array = [];
        foreach ($q as $r) {
            $array[] = $r;
        }
        $select = grading_scale(_h($r['grade']));
        return $app->hook->apply_filter('grades', $select);
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
}

/**
 * Admit status: shows general list of admission statuses and
 * if $status is not NULL, shows the admit status
 * for a particular applicant.
 *
 * @since 1.0.0
 * @param string $status            
 * @return string Returns the application admit status if selected is true.
 */
function admit_status_select($status = NULL)
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="admitStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                <option value="">&nbsp;</option>
                <option value="FF"' . selected($status, 'FF', false) . '>' . _t('FF First Time Freshman') . '</option>
                <option value="TR"' . selected($status, 'TR', false) . '>' . _t('TR Transfer') . '</option>
                <option value="RA"' . selected($status, 'RA', false) . '>' . _t('RA Readmit') . '</option>
                <option value="NA"' . selected($status, 'NA', false) . '>' . _t('NA Non-Applicable') . '</option>
                </select>';
    return $app->hook->apply_filter('admit_status', $select, $status);
}

/**
 * General Ledger type select: shows general list of general
 * ledger types and if $type is not NULL, shows the general
 * ledger type for a particular general ledger record.
 *
 * @since 1.1.5
 * @param string $type            
 * @return string Returns the record type if selected is true.
 */
function general_ledger_type_select($type = NULL)
{
    $app = \Liten\Liten::getInstance();

    $select = '<select name="gl_acct_type" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="' . _t('Asset') . '"' . selected($type, _t('Asset'), false) . '>' . _t('Asset') . '</option>
                <option value="' . _t('Liability') . '"' . selected($type, _t('Liability'), false) . '>' . _t('Liability') . '</option>
                <option value="' . _t('Equity') . '"' . selected($type, _t('Equity'), false) . '>' . _t('Equity') . '</option>
                <option value="' . _t('Revenue') . '"' . selected($type, _t('Revenue'), false) . '>' . _t('Revenue') . '</option>
                <option value="' . _t('Expense') . '"' . selected($type, _t('Expense'), false) . '>' . _t('Expense') . '</option>
                </select>';
    return $app->hook->apply_filter('general_ledger_type', $select, $type);
}

function get_user_avatar($email, $s = 80, $class = '', $d = 'mm', $r = 'g', $img = false)
{
    $app = \Liten\Liten::getInstance();

    if ($app->hook->has_filter('base_url')) {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }

    $url = $protocol . "www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=200&d=$d&r=$r";

    if (get_http_response_code($protocol . 'www.gravatar.com/') != 302) {
        $static_image_url = get_base_url() . "static/assets/img/avatar.png?s=200";
        $avatarsize = getimagesize($static_image_url);
        $avatar = '<img src="' . get_base_url() . 'static/assets/img/avatar.png" ' . resize_image($avatarsize[1], $avatarsize[1], $s) . ' class="' . $class . '" />';
    } else {
        $avatarsize = getimagesize($url);
        $avatar = '<img src="' . $url . '" ' . resize_image($avatarsize[1], $avatarsize[1], $s) . ' class="' . $class . '" />';
    }

    return $app->hook->apply_filter('user_avatar', $avatar, $email, $s, $class, $d, $r, $img);
}

function nocache_headers()
{
    $app = \Liten\Liten::getInstance();

    $headers = [
        'Expires' => 'Sun, 01 Jan 2014 00:00:00 GMT',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache'
    ];
    foreach ($headers as $k => $v) {
        header("{$k}: {$v}");
    }
    return $app->hook->apply_filter('nocache_headers', $headers);
}

/**
 * WYSIWYG editor function for myetSIS
 * self service portal.
 *
 * @since 6.1.12
 */
function myetsis_wysiwyg_editor()
{
    $app = \Liten\Liten::getInstance();

    $editor = '<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>' . "\n";
    $editor .= '<script type="text/javascript">
    tinymce.init({
        selector: "textarea",
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image' . $app->hook->do_action('myetsis_wysiwyg_editor_toolbar') . '",
        autosave_ask_before_unload: false
    });
    </script>' . "\n";
    return $app->hook->apply_filter('myetsis_wysiwyg_editor', $editor);
}

/**
 * Compares release values.
 *
 * @since 6.1.14
 * @param string $current
 *            Current release value.
 * @param string $latest
 *            Latest release value.
 * @param string $operator
 *            Operand use to compare current and latest release values.
 * @return bool
 */
function compare_releases($current, $latest, $operator = '>')
{
    $app = \Liten\Liten::getInstance();

    $php_function = version_compare($latest, $current, $operator);
    /**
     * Filters the comparison between two release.
     *
     * @since 6.1.14
     * @param $php_function PHP
     *            function for comparing two release values.
     */
    $release = $app->hook->apply_filter('compare_releases', $php_function);

    if ($release) {
        return $latest;
    } else {
        return false;
    }
}

/**
 * Retrieves a response code from the header
 * of a given resource.
 *
 * @since 6.1.14
 * @param string $url
 *            URL of resource/website.
 * @return int HTTP response code.
 */
function get_http_response_code($url)
{
    $app = \Liten\Liten::getInstance();

    $headers = get_headers($url);
    $status = substr($headers[0], 9, 3);
    /**
     * Filters the http response code.
     *
     * @since 6.1.14
     * @param
     *            string
     */
    return $app->hook->apply_filter('http_response_code', $status);
}

/**
 * Plugin success message when plugin is activated successfully.
 *
 * @since 6.2.0
 * @param string $plugin_name
 *            The name of the plugin that was just activated.
 */
function etsis_plugin_activate_message($plugin_name)
{
    $app = \Liten\Liten::getInstance();
    $success = _etsis_flash()->success(_t('Plugin <strong>activated</strong>.'));
    /**
     * Filter the default plugin success activation message.
     *
     * @since 6.2.0
     * @param string $success
     *            The success activation message.
     * @param string $plugin_name
     *            The name of the plugin that was just activated.
     */
    return $app->hook->apply_filter('etsis_plugin_activate_message', $success, $plugin_name);
}

/**
 * Plugin success message when plugin is deactivated successfully.
 *
 * @since 6.2.0
 * @param string $plugin_name
 *            The name of the plugin that was just deactivated.
 */
function etsis_plugin_deactivate_message($plugin_name)
{
    $app = \Liten\Liten::getInstance();
    $success = _etsis_flash()->success(_t('Plugin <strong>deactivated</strong>.'));
    /**
     * Filter the default plugin success deactivation message.
     *
     * @since 6.2.0
     * @param string $success
     *            The success deactivation message.
     * @param string $plugin_name
     *            The name of the plugin that was just deactivated.
     */
    return $app->hook->apply_filter('etsis_plugin_deactivate_message', $success, $plugin_name);
}

/**
 * Dropdown list of active academic programs.
 *
 * @since 6.2.3
 * @param string $progCode
 *            Academic program code.
 */
function acad_program_select($progCode = null)
{
    $app = \Liten\Liten::getInstance();
    try {
        $prog = $app->db->acad_program()
            ->where('currStatus = "A"')
            ->orderBy('deptCode');
        $query = $prog->find();

        foreach ($query as $r) {
            echo '<option value="' . _h($r->acadProgCode) . '"' . selected($progCode, _h($r->acadProgCode), false) . '>' . _h($r->acadProgCode) . ' ' . _h($r->acadProgTitle) . '</option>' . "\n";
        }

        return $app->hook->apply_filter('academic_program', $query, $progCode);
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
}

/**
 * Dashboard router function.
 * 
 * Includes dashboard router filter (dashboard_router).
 *
 * @since 6.2.7
 */
function _etsis_dashboard_router()
{
    $app = \Liten\Liten::getInstance();

    $router = $app->config('routers_dir') . 'dashboard.router.php';
    if (!$app->hook->has_filter('dashboard_router')) {
        require($router);
    }
    return $app->hook->apply_filter('dashboard_router', $router);
}

/**
 * Application (APPL) router function.
 * 
 * Includes application router filter (appl_router).
 *
 * @since 6.2.7
 */
function _etsis_appl_router()
{
    $app = \Liten\Liten::getInstance();

    $router = $app->config('routers_dir') . 'appl.router.php';
    if (!$app->hook->has_filter('appl_router')) {
        require($router);
    }
    return $app->hook->apply_filter('appl_router', $router);
}

/**
 * Course (CRSE) router function.
 * 
 * Includes course router filter (crse_router).
 *
 * @since 6.2.7
 */
function _etsis_crse_router()
{
    $app = \Liten\Liten::getInstance();

    $router = $app->config('routers_dir') . 'course.router.php';
    if (!$app->hook->has_filter('crse_router')) {
        require($router);
    }
    return $app->hook->apply_filter('crse_router', $router);
}

/**
 * Name and address (NAE) router function.
 * 
 * Includes name and address router filter (nae_router).
 *
 * @since 6.2.7
 */
function _etsis_nae_router()
{
    $app = \Liten\Liten::getInstance();

    $router = $app->config('routers_dir') . 'person.router.php';
    if (!$app->hook->has_filter('nae_router')) {
        require($router);
    }
    return $app->hook->apply_filter('nae_router', $router);
}

/**
 * Course section (SECT) router function.
 * 
 * Includes course section router filter (sect_router).
 *
 * @since 6.2.7
 */
function _etsis_sect_router()
{
    $app = \Liten\Liten::getInstance();

    $router = $app->config('routers_dir') . 'section.router.php';
    if (!$app->hook->has_filter('sect_router')) {
        require($router);
    }
    return $app->hook->apply_filter('sect_router', $router);
}

/**
 * Academic program (PROG) router function.
 * 
 * Includes academic program router filter (prog_router).
 *
 * @since 6.2.7
 */
function _etsis_prog_router()
{
    $app = \Liten\Liten::getInstance();

    $router = $app->config('routers_dir') . 'program.router.php';
    if (!$app->hook->has_filter('prog_router')) {
        require($router);
    }
    return $app->hook->apply_filter('prog_router', $router);
}

/**
 * Student router function.
 * 
 * Includes student router filter (student_router).
 *
 * @since 6.2.7
 */
function _etsis_student_router()
{
    $app = \Liten\Liten::getInstance();

    $router = $app->config('routers_dir') . 'student.router.php';
    if (!$app->hook->has_filter('student_router')) {
        require($router);
    }
    return $app->hook->apply_filter('student_router', $router);
}

/**
 * myetSIS router function.
 *
 * @since 6.3.0
 */
function _etsis_myetsis_router()
{
    $app = \Liten\Liten::getInstance();

    $router = $app->config('routers_dir') . 'myetsis.router.php';
    if (!$app->hook->has_filter('myetsis_router')) {
        require($router);
    }
    return $app->hook->apply_filter('myetsis_router', $router);
}

/**
 * Index router function.
 *
 * @since 6.3.0
 */
function _etsis_index_router()
{
    $app = \Liten\Liten::getInstance();

    $router = $app->config('routers_dir') . 'index.router.php';
    if (!$app->hook->has_filter('index_router')) {
        require($router);
    }
    return $app->hook->apply_filter('index_router', $router);
}

/**
 * Register stylesheet.
 * 
 * @since 6.3.0
 * @param string $handle
 */
function etsis_register_style($handle)
{
    $app = \Liten\Liten::getInstance();
    return $app->asset->register_style($handle);
}

/**
 * Register javascript.
 * 
 * @since 6.3.0
 * @param string $handle
 */
function etsis_register_script($handle)
{
    $app = \Liten\Liten::getInstance();
    return $app->asset->register_script($handle);
}

/**
 * Enqueue stylesheet.
 * 
 * @since 6.3.0
 */
function etsis_enqueue_style()
{
    $app = \Liten\Liten::getInstance();
    echo $app->asset->enqueue_style();
}

/**
 * Enqueue javascript.
 * 
 * @since 6.3.0
 */
function etsis_enqueue_script()
{
    $app = \Liten\Liten::getInstance();
    echo $app->asset->enqueue_script();
}

/**
 * Shows an error message when system is in DEV mode.
 * 
 * @since 6.3.0
 */
function etsis_dev_mode()
{
    if (APP_ENV === 'DEV') {
        echo '<div class="alert dismissable alert-danger center sticky">'._t('Your system is currently in DEV mode. Please remember to set your system back to PROD mode after testing. When PROD mode is set, this warning message will disappear.').'</div>';
    }
}
$app->hook->add_action('etsis_dashboard_head', 'head_release_meta', 5);
$app->hook->add_action('etsis_dashboard_head', 'etsis_enqueue_style', 1);
$app->hook->add_action('etsis_dashboard_head', 'etsis_notify_style', 2);
$app->hook->add_action('myetsis_head', 'head_release_meta', 5);
$app->hook->add_action('etsis_dashboard_footer', 'etsis_notify_script', 20);
$app->hook->add_action('etsis_dashboard_footer', 'etsis_enqueue_script', 5);
$app->hook->add_action('release', 'foot_release', 5);
$app->hook->add_action('dashboard_top_widgets', 'dashboard_student_count', 5);
$app->hook->add_action('dashboard_top_widgets', 'dashboard_course_count', 5);
$app->hook->add_action('dashboard_top_widgets', 'dashboard_acadProg_count', 5);
$app->hook->add_action('dashboard_right_widgets', 'dashboard_clock', 5);
$app->hook->add_action('dashboard_right_widgets', 'dashboard_weather', 5);
$app->hook->add_action('activated_plugin', 'etsis_plugin_activate_message', 5, 1);
$app->hook->add_action('deactivated_plugin', 'etsis_plugin_deactivate_message', 5, 1);
$app->hook->add_action('login_form_top', 'etsis_login_form_show_message', 5);
$app->hook->add_action('execute_reg_rest_rule', 'etsis_reg_rest_rule', 5, 1);
$app->hook->add_action('post_save_myetsis_reg', 'create_update_sttr_record', 5, 1);
$app->hook->add_action('post_rgn_stu_crse_reg', 'create_update_sttr_record', 5, 1);
$app->hook->add_action('post_brgn_stu_crse_reg', 'create_update_sttr_record', 5, 1);
$app->hook->add_action('dashboard_admin_notices', 'etsis_dev_mode', 5);
$app->hook->add_action('myetsis_admin_notices', 'etsis_dev_mode', 5);
$app->hook->add_filter('the_myetsis_page_content', 'etsis_autop');
$app->hook->add_filter('the_myetsis_page_content', 'parsecode_unautop');
$app->hook->add_filter('the_myetsis_page_content', 'do_parsecode', 5);
$app->hook->add_filter('the_myetsis_welcome_message', 'etsis_autop');
$app->hook->add_filter('the_myetsis_welcome_message', 'parsecode_unautop');
$app->hook->add_filter('the_myetsis_welcome_message', 'do_parsecode', 5);
$app->hook->add_filter('etsis_authenticate_person', 'etsis_authenticate', 5, 3);
$app->hook->add_filter('etsis_auth_cookie', 'etsis_set_auth_cookie', 5, 2);

<?php
if (! defined('BASE_PATH'))
    exit('No direct script access allowed');
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
    $menu = APP_PATH . 'views/dashboard/menu.php';
    if (! has_filter('core_sidebar_menu')) {
        include ($menu);
    }
    return apply_filter('core_sidebar_menu', $menu, $screen, $crse, $sect, $nae, $staff, $spro, $prog);
}

/**
 * Core admin bar include.
 *
 * @since 6.1.15
 */
function core_admin_bar()
{
    $filename = APP_PATH . 'views/dashboard/core-admin-bar.php';
    
    if (! is_readable($filename)) {
        __return_false();
    }
    
    if (! has_filter('core_admin_bar')) {
        include ($filename);
    }
    return apply_filter('core_admin_bar', $filename);
}

/**
 * Wrapper function for the core PHP function: trigger_error.
 *
 * This function makes the error a little more understandable for the
 * end user to track down the issue.
 *
 * @since 6.1.15
 * @param string $message
 *            Custom message to print.
 * @param string $level
 *            Predefined PHP error constant.
 */
function _trigger_error($message, $level = E_USER_NOTICE)
{
    $debug = debug_backtrace();
    $caller = next($debug);
    echo '<div class="alerts alerts-error center">';
    trigger_error($message . ' in <strong>' . $caller['function'] . '</strong> called from <strong>' . $caller['file'] . '</strong> on line <strong>' . $caller['line'] . '</strong>' . "\n<br />error handler", $level);
    echo '</div>';
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
 * @since 6.1.15
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
    /**
     * Fires when a deprecated function is called.
     *
     * @since 6.1.15
     *       
     * @param string $function_name
     *            The function that was called.
     * @param string $replacement
     *            The function that should have been called.
     * @param string $release
     *            The release of eduTrac SIS that deprecated the function.
     */
    do_action('deprecated_function_run', $function_name, $replacement, $release);
    
    /**
     * Filter whether to trigger an error for deprecated functions.
     *
     * @since 6.1.15
     *       
     * @param bool $trigger
     *            Whether to trigger the error for deprecated functions. Default true.
     */
    if (APP_ENV == 'DEV' && apply_filter('deprecated_function_trigger_error', true)) {
        if (function_exists('_t')) {
            if (! is_null($replacement)) {
                _trigger_error(sprintf(_t('%1$s is <strong>deprecated</strong> since release %2$s! Use %3$s instead.'), $function_name, $release, $replacement));
            } else {
                _trigger_error(sprintf(_t('%1$s is <strong>deprecated</strong> since release %2$s with no alternative available.'), $function_name, $release));
            }
        } else {
            if (! is_null($replacement)) {
                _trigger_error(sprintf('%1$s is <strong>deprecated</strong> since release %2$s! Use %3$s instead.', $function_name, $release, $replacement));
            } else {
                _trigger_error(sprintf('%1$s is <strong>deprecated</strong> since release %2$s with no alternative available.', $function_name, $release));
            }
        }
    }
}

/**
 * Prints copyright in the dashboard footer.
 *
 * @since 6.1.15
 */
function etsis_dashboard_copyright_footer()
{
    $copyright = '<!--  Copyright Line -->' . "\n";
    $copyright .= '<div class="copy">' . _t('&copy; 2013') . ' - ' . foot_release() . ' &nbsp; <a href="http://www.litenframework.com/"><img src="' . get_base_url() . 'static/assets/images/button.png" alt="Built with Liten Framework"/></a></div>' . "\n";
    $copyright .= '<!--  End Copyright Line -->' . "\n";
    
    return apply_filter('etsis_copyright', $copyright);
}
/**
 * Includes and loads all activated plugins.
 *
 * @since 1.0.0
 */
load_activated_plugins(APP_PATH . 'plugins/');

/**
 * Includes and loads all available modules.
 *
 * @since 5.0.0
 */
$app->module->load_installed_modules(APP_PATH . 'modules/');

/**
 * An action called to add the plugin's link
 * to the menu structure.
 *
 * @since 1.0.0
 * @uses do_action() Calls 'admin_menu' hook.
 */
do_action('admin_menu');

/**
 * An action called to add custom page links
 * to menu structure.
 *
 * @since 4.2.0
 * @uses do_action() Calls 'custom_plugin_page' hook.
 */
do_action('custom_plugin_page');

/**
 * An action called to create db tables needed
 * for a plugin
 *
 * @see Plugin::register_activation_hook()
 *
 * @since 4.2.0
 * @deprecated since release 6.1.06
 * @uses do_action() Calls 'create_db_table' hook.
 */
do_action('create_db_table');

/**
 * Fires the init action.
 *
 * @since 1.0.0
 */
function init()
{
    /**
     * Fires after eduTrac SIS has finished loading but before any headers are sent.
     *
     * @since 1.0.0
     */
    do_action('init');
}

/**
 * Fires the admin_head action.
 *
 * @since 1.0.0
 */
function admin_head()
{
    /**
     * Prints scripts and/or data in the head tag of the dashboard.
     *
     * @since 1.0.0
     */
    do_action('admin_head');
}

/**
 * Fires the myet_head action.
 *
 * @since 1.0.0
 */
function myet_head()
{
    /**
     * Prints scripts and/or data in the head tag of the myeduTrac self service
     * portal.
     *
     * @since 1.0.0
     */
    do_action('myet_head');
}

/**
 * Fires the footer action via the dashboard.
 *
 * @since 1.0.0
 */
function footer()
{
    /**
     * Prints scripts and/or data before the ending body tag
     * of the dashboard.
     *
     * @since 1.0.0
     */
    do_action('footer');
}

/**
 * Fires the footer action via
 * myeduTrac self service portal.
 *
 * @since 6.1.12
 */
function myet_footer()
{
    /**
     * Prints scripts and/or data before the ending body tag of the myeduTrac
     * self service portal.
     *
     * @since 6.1.12
     */
    do_action('myet_footer');
}

/**
 * Fires the release action.
 *
 * @since 1.0.0
 */
function release()
{
    /**
     * Prints eduTrac SIS release information.
     *
     * @since 1.0.0
     */
    do_action('release');
}

/**
 * Fires the dashboard_top_widgets action.
 *
 * @since 1.0.0
 */
function dashboard_top_widgets()
{
    /**
     * Prints widgets at the top portion of the dashboard.
     *
     * @since 1.0.0
     */
    do_action('dashboard_top_widgets');
}

/**
 * Fires the dashboard_right_widgets action.
 *
 * @deprecated since 5.0.0
 * @since 1.0.0
 */
function dashboard_right_widgets()
{
    /**
     * Prints widgets on the right side of the dashboard.
     *
     * @deprecated since 5.0.0
     * @since 1.0.0
     */
    do_action('dashboard_right_widgets');
}

/**
 * Shows number of active students.
 *
 * @since 4.0.0
 */
function dashboard_student_count()
{
    $app = \Liten\Liten::getInstance();
    $stu = $app->db->student()
        ->select('COUNT(student.stuID) as count')
        ->_join('stu_program', 'student.stuID = stu_program.stuID')
        ->where('student.status = "A"')
        ->_and_()
        ->where('stu_program.currStatus = "A"');
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
    echo apply_filter('dashboard_student_count', $stuCount);
}

/**
 * Shows number of active courses.
 *
 * @since 4.0.0
 */
function dashboard_course_count()
{
    $app = \Liten\Liten::getInstance();
    
    $count = $app->db->course()
        ->where('course.currStatus = "A" AND course.endDate = "0000-00-00"')
        ->count('course.courseID');
    
    $crseCount = '<div class="col-md-4">';
    $crseCount .= '<a href="#" class="widget-stats widget-stats-1 widget-stats-inverse">';
    $crseCount .= '<span class="glyphicons book"><i></i><span class="txt">' . _t('Active Courses') . '</span></span>';
    $crseCount .= '<div class="clearfix"></div>';
    $crseCount .= '<span class="count">' . $count . '</span>';
    $crseCount .= '</a>';
    $crseCount .= '</div>';
    echo apply_filter('dashboard_course_count', $crseCount);
}

/**
 * Shows number of active academic programs.
 *
 * @since 4.0.0
 */
function dashboard_acadProg_count()
{
    $app = \Liten\Liten::getInstance();
    
    $count = $app->db->acad_program()
        ->where('acad_program.currStatus = "A" AND acad_program.endDate = "0000-00-00"')
        ->count('acad_program.acadProgID');
    
    $progCount = '<div class="col-md-4">';
    $progCount .= '<a href="#" class="widget-stats widget-stats-1 widget-stats-inverse">';
    $progCount .= '<span class="glyphicons keynote"><i></i><span class="txt">' . _t('Active Programs') . '</span></span>';
    $progCount .= '<div class="clearfix"></div>';
    $progCount .= '<span class="count">' . $count . '</span>';
    $progCount .= '</a>';
    $progCount .= '</div>';
    echo apply_filter('dashboard_acadProg_count', $progCount);
}

/**
 * Shows update message when a new release of
 * eduTrac SIS is available.
 *
 * @since 4.0.0
 */
function show_update_message()
{
    $app = \Liten\Liten::getInstance();
    $acl = new \app\src\ACL(get_persondata('personID'));
    if ($acl->userHasRole(8)) {
        $update = new \VisualAppeal\AutoUpdate(rtrim($app->config('file.savepath'), '/'), BASE_PATH, 1800);
        $update->setCurrentVersion(RELEASE_TAG);
        $update->setUpdateUrl('http://edutrac.s3.amazonaws.com/core/1.1/update-check');
        
        // Optional:
        $update->addLogHandler(new Monolog\Handler\StreamHandler(APP_PATH . 'tmp/logs/core-update.' . date('m-d-Y') . '.txt'));
        $update->setCache(new Desarrolla2\Cache\Adapter\File(APP_PATH . 'tmp/cache'), 3600);
        if ($update->checkUpdate() !== false) {
            if ($update->newVersionAvailable()) {
                $alert = '<div class="alerts alerts-warn center">';
                $alert .= sprintf(_t('eduTrac SIS release %s is available for download/upgrade.'), $update->getLatestVersion());
                $alert .= '</div>';
            }
        }
    }
    return apply_filter('update_message', $alert);
}

/**
 * Retrieve javascript directory uri.
 *
 * @since 4.1.9
 * @uses apply_filter() Calls 'javascript_directory_uri' filter.
 *      
 * @return string eduTrac javascript url.
 */
function get_javascript_directory_uri()
{
    $directory = 'static/assets/components';
    $javascript_root_uri = get_base_url();
    $javascript_dir_uri = "$javascript_root_uri$directory/";
    return apply_filter('javascript_directory_uri', $javascript_dir_uri, $javascript_root_uri, $directory);
}

/**
 * Retrieve less directory uri.
 *
 * @since 4.1.9
 * @uses apply_filter() Calls 'less_directory_uri' filter.
 *      
 * @return string eduTrac less url.
 */
function get_less_directory_uri()
{
    $directory = 'static/assets/less';
    $less_root_uri = get_base_url();
    $less_dir_uri = "$less_root_uri$directory/";
    return apply_filter('less_directory_uri', $less_dir_uri, $less_root_uri, $directory);
}

/**
 * Retrieve css directory uri.
 *
 * @since 4.1.9
 * @uses apply_filter() Calls 'css_directory_uri' filter.
 *      
 * @return string eduTrac css url.
 */
function get_css_directory_uri()
{
    $directory = 'static/assets/css';
    $css_root_uri = get_base_url();
    $css_dir_uri = "$css_root_uri$directory/";
    return apply_filter('css_directory_uri', $css_dir_uri, $css_root_uri, $directory);
}

/**
 * Parses a string into variables to be stored in an array.
 *
 * Uses {@link http://www.php.net/parse_str parse_str()}
 *
 * @since 4.2.0
 * @param string $string
 *            The string to be parsed.
 * @param array $array
 *            Variables will be stored in this array.
 */
function et_parse_str($string, &$array)
{
    parse_str($string, $array);
    /**
     * Filter the array of variables derived from a parsed string.
     *
     * @since 4.2.0
     * @param array $array
     *            The array populated with variables.
     */
    $array = apply_filter('et_parse_str', $array);
}

/**
 * Frontend portal site title.
 *
 * @since 4.3
 * @uses apply_filter() Calls 'met_title' filter.
 *      
 * @return string eduTrac frontend site title.
 */
function get_met_title()
{
    $title = '<em>' . _t('my') . '</em>' . ('eduTrac');
    return apply_filter('met_title', $title);
}

/**
 * Frontend portal footer powered by and release.
 *
 * @since 4.3
 * @uses apply_filter() Calls 'met_footer_release' filter.
 *      
 * @return mixed.
 */
function get_met_footer_release()
{
    if (CURRENT_RELEASE != RELEASE_TAG) {
        $release = _t('Powered by eduTrac SIS r') . CURRENT_RELEASE . ' (t' . RELEASE_TAG . ')';
    } else {
        $release = _t('Powered by eduTrac SIS r') . CURRENT_RELEASE;
    }
    return apply_filter('met_footer_release', $release);
}

/**
 * Frontend portal footer title.
 *
 * @since 4.3
 * @uses apply_filter() Calls 'met_footer_title' filter.
 *      
 * @return string
 */
function get_met_footer_title()
{
    $title = '<em>' . _t('my') . '</em>' . ('eduTrac');
    return apply_filter('met_footer_title', $title);
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
    $select = '<select name="addressType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
            <option value="">&nbsp;</option>
            <option value="B"' . selected($typeCode, 'B', false) . '>Business</option>
            <option value="H"' . selected($typeCode, 'H', false) . '>Home/Mailing</option>
            <option value="P"' . selected($typeCode, 'P', false) . '>Permanent</option>
            </select>';
    return apply_filter('address_type', $select, $typeCode);
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
    $select = '<select name="deptTypeCode" id="deptType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
            <option value="">&nbsp;</option>
            <option value="ADMIN"' . selected($typeCode, 'ADMIN', false) . '>' . _t('Administrative') . '</option>
            <option value="ACAD"' . selected($typeCode, 'ACAD', false) . '>' . _t('Academic') . '</option>
            </select>';
    return apply_filter('dept_type', $select, $typeCode);
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
    $select = '<select name="addressStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
			<option value="">&nbsp;</option>
	    	<option value="C"' . selected($status, 'C', false) . '>Current</option>
			<option value="I"' . selected($status, 'I', false) . '>Inactive</option>
		    </select>';
    return apply_filter('address_status', $select, $status);
}

/**
 * Acad Level select: shows general list of academic levels and
 * if $levelCode is not NULL, shows the academic level attached
 * to a particular record.
 *
 * @since 1.0.0
 * @param string $levelCode            
 * @return string Returns the record key if selected is true.
 */
function acad_level_select($levelCode = null, $readonly = null, $required = '')
{
    $select = '<select name="acadLevelCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"' . $readonly . $required . '>
            <option value="">&nbsp;</option>
            <option value="NA"' . selected($levelCode, 'NA', false) . '>N/A Not Applicable</option>
            <option value="CE"' . selected($levelCode, 'CE', false) . '>CE Continuing Education</option>
            <option value="CTF"' . selected($levelCode, 'CTF', false) . '>CTF Certificate</option>
            <option value="UG"' . selected($levelCode, 'UG', false) . '>UG Undergraduate</option>
            <option value="GR"' . selected($levelCode, 'GR', false) . '>GR Graduate</option>
            <option value="DIP"' . selected($levelCode, 'DIP', false) . '>DIP Diploma</option>
            <option value="PR"' . selected($levelCode, 'PR', false) . '>PR Professional</option>
            <option value="PhD"' . selected($levelCode, 'PhD', false) . '>PhD Doctorate</option>
            </select>';
    return apply_filter('acad_level', $select, $levelCode);
}

/**
 * Fee acad Level select: shows general list of academic levels and
 * if $levelCode is not NULL, shows the academic level attached
 * to a particular record.
 *
 * @since 4.1.7
 * @param string $levelCode            
 * @return string Returns the record key if selected is true.
 */
function fee_acad_level_select($levelCode = null)
{
    $select = '<select name="acadLevelCode" class="form-control">
            <option value="">&nbsp;</option>
            <option value="NA"' . selected($levelCode, 'NA', false) . '>N/A Not Applicable</option>
            <option value="CE"' . selected($levelCode, 'CE', false) . '>CE Continuing Education</option>
            <option value="CTF"' . selected($levelCode, 'CTF', false) . '>CTF Certificate</option>
            <option value="UG"' . selected($levelCode, 'UG', false) . '>UG Undergraduate</option>
            <option value="GR"' . selected($levelCode, 'GR', false) . '>GR Graduate</option>
            <option value="DIP"' . selected($levelCode, 'DIP', false) . '>DIP Diploma</option>
            <option value="PR"' . selected($levelCode, 'PR', false) . '>PR Professional</option>
            <option value="PhD"' . selected($levelCode, 'PhD', false) . '>PhD Doctorate</option>
            </select>';
    return apply_filter('fee_acad_level', $select, $levelCode);
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
    $select = '<select name="currStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"' . $readonly . ' required>
    			<option value="">&nbsp;</option>
    	    	<option value="A"' . selected($status, 'A', false) . '>A Active</option>
    	    	<option value="I"' . selected($status, 'I', false) . '>I Inactive</option>
    			<option value="P"' . selected($status, 'P', false) . '>P Pending</option>
    			<option value="O"' . selected($status, 'O', false) . '>O Obsolete</option>
		        </select>';
    return apply_filter('status', $select, $status);
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
    $select = '<select name="currStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required' . $readonly . '>
    			<option value="">&nbsp;</option>
    	    	<option' . dopt('activate_course_sec') . ' value="A"' . selected($status, 'A', false) . '>A Active</option>
    	    	<option value="I"' . selected($status, 'I', false) . '>I Inactive</option>
    			<option value="P"' . selected($status, 'P', false) . '>P Pending</option>
    			<option' . dopt('cancel_course_sec') . ' value="C"' . selected($status, 'C', false) . '>C Cancel</option>
    			<option value="O"' . selected($status, 'O', false) . '>O Obsolete</option>
		        </select>';
    return apply_filter('status', $select, $status);
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
    $select = '<select name="personType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="FAC"' . selected($type, 'FAC', false) . '>FAC Faculty</option>
                <option value="ADJ"' . selected($type, 'ADJ', false) . '>ADJ Adjunct</option>
                <option value="STA"' . selected($type, 'STA', false) . '>STA Staff</option>
                <option value="APL"' . selected($type, 'APL', false) . '>APL Applicant</option>
                <option value="STU"' . selected($type, 'STU', false) . '>STU Student</option>
                </select>';
    return apply_filter('person_type', $select, $type);
}

/**
 * Course Level dropdown: shows general list of course levels and
 * if $levelCode is not NULL, shows the course level attached
 * to a particular record.
 *
 * @since 1.0.0
 * @param string $levelCode            
 * @return string Returns the record key if selected is true.
 */
function course_level_select($levelCode = NULL, $readonly = null)
{
    $select = '<select name="courseLevelCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required' . $readonly . '>
			<option value="">&nbsp;</option>
	    	<option value="100"' . selected($levelCode, '100', false) . '>100 Course Level</option>
			<option value="200"' . selected($levelCode, '200', false) . '>200 Course Level</option>
			<option value="300"' . selected($levelCode, '300', false) . '>300 Course Level</option>
			<option value="400"' . selected($levelCode, '400', false) . '>400 Course Level</option>
			<option value="500"' . selected($levelCode, '500', false) . '>500 Course Level</option>
			<option value="600"' . selected($levelCode, '600', false) . '>600 Course Level</option>
			<option value="700"' . selected($levelCode, '700', false) . '>700 Course Level</option>
			<option value="800"' . selected($levelCode, '800', false) . '>800 Course Level</option>
			<option value="900"' . selected($levelCode, '900', false) . '>900 Course Level</option>
		    </select>';
    return apply_filter('course_level', $select, $levelCode);
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
    return apply_filter('instructor_method', $select, $method);
}

/**
 * Student Course section status select: shows general list of course sec statuses and
 * if $status is not NULL, shows the status
 * for a particular student course section record.
 *
 * @since 1.0.0
 * @param string $status            
 * @return string Returns the record status if selected is true.
 */
function stu_course_sec_status_select($status = NULL, $readonly = '')
{
    $select = '<select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required' . $readonly . '>
                <option value="">&nbsp;</option>
                <option value="A"' . selected($status, 'A', false) . '>' . _t('A Add') . '</option>
                <option value="N"' . selected($status, 'N', false) . '>' . _t('N New') . '</option>
                <option value="D"' . selected($status, 'D', false) . '>' . _t('D Drop') . '</option>
                <option value="W"' . selected($status, 'W', false) . '>' . _t('W Withdrawn') . '</option>
                <option value="C"' . selected($status, 'C', false) . '>' . _t('C Cancelled') . '</option>
                </select>';
    return apply_filter('course_sec_status', $select, $status);
}

/**
 * Student program status select: shows general list of student
 * statuses and if $status is not NULL, shows the status
 * for a particular student program record.
 *
 * @since 1.0.0
 * @param string $status            
 * @return string Returns the record status if selected is true.
 */
function stu_prog_status_select($status = NULL)
{
    $select = '<select name="currStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="A"' . selected($status, 'A', false) . '>' . _t('A Active') . '</option>
                <option value="P"' . selected($status, 'P', false) . '>' . _t('P Potential') . '</option>
                <option value="W"' . selected($status, 'W', false) . '>' . _t('W Withdrawn') . '</option>
                <option value="C"' . selected($status, 'C', false) . '>' . _t('C Changed Mind') . '</option>
                <option value="G"' . selected($status, 'G', false) . '>' . _t('G Graduated') . '</option>
                </select>';
    return apply_filter('stu_prog_status', $select, $status);
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
    $select = '<select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="I"' . selected($status, 'I', false) . '>' . _t('I Institutional') . '</option>
                <option value="TR"' . selected($status, 'TR', false) . '>' . _t('TR Transfer') . '</option>
                <option value="AP"' . selected($status, 'AP', false) . '>' . _t('AP Advanced Placement') . '</option>
                <option value="X"' . selected($status, 'X', false) . '>' . _t('X Exempt') . '</option>
                <option value="T"' . selected($status, 'T', false) . '>' . _t('T Test') . '</option>
                </select>';
    return apply_filter('course_sec_status', $select, $status);
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
    return apply_filter('class_year', $select, $year);
}

/**
 * Grading scale: shows general list of letter grades and
 * if $grade is not NULL, shows the grade
 * for a particular student course section record
 *
 * @since 1.0.0
 * @param string $grade            
 * @return string Returns the stu_course_sec grade if selected is true.
 */
function grading_scale($grade = NULL)
{
    $app = \Liten\Liten::getInstance();
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
    return apply_filter('grading_scale', $select, $grade);
}

function grades($id, $aID)
{
    $app = \Liten\Liten::getInstance();
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
    return apply_filter('grades', $select);
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
    $select = '<select name="admitStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                <option value="">&nbsp;</option>
                <option value="FF"' . selected($status, 'FF', false) . '>' . _t('FF First Time Freshman') . '</option>
                <option value="TR"' . selected($status, 'TR', false) . '>' . _t('TR Transfer') . '</option>
                <option value="RA"' . selected($status, 'RA', false) . '>' . _t('RA Readmit') . '</option>
                <option value="NA"' . selected($status, 'NA', false) . '>' . _t('NA Non-Applicable') . '</option>
                </select>';
    return apply_filter('admit_status', $select, $status);
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
    $select = '<select name="gl_acct_type" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="' . _t('Asset') . '"' . selected($type, _t('Asset'), false) . '>' . _t('Asset') . '</option>
                <option value="' . _t('Liability') . '"' . selected($type, _t('Liability'), false) . '>' . _t('Liability') . '</option>
                <option value="' . _t('Equity') . '"' . selected($type, _t('Equity'), false) . '>' . _t('Equity') . '</option>
                <option value="' . _t('Revenue') . '"' . selected($type, _t('Revenue'), false) . '>' . _t('Revenue') . '</option>
                <option value="' . _t('Expense') . '"' . selected($type, _t('Expense'), false) . '>' . _t('Expense') . '</option>
                </select>';
    return apply_filter('general_ledger_type', $select, $type);
}

function get_user_avatar($email, $s = 80, $class = '', $d = 'mm', $r = 'g', $img = false)
{
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=200&d=$d&r=$r";
    $avatarsize = getimagesize($url);
    $avatar = '<img src="' . $url . '" ' . imgResize($avatarsize[1], $avatarsize[1], $s) . ' class="' . $class . '" />';
    return apply_filter('user_avatar', $avatar, $email, $s, $class, $d, $r, $img);
}

function nocache_headers()
{
    $headers = [
        'Expires' => 'Sun, 01 Jan 2014 00:00:00 GMT',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache'
    ];
    foreach ($headers as $k => $v) {
        header("{$k}: {$v}");
    }
    return apply_filter('nocache_headers', $headers);
}

/**
 * WYSIWYG editor function for myeduTrac
 * self service portal.
 *
 * @since 6.1.12
 */
function myet_wysiwyg_editor()
{
    $editor = '<script src="//tinymce.cachefly.net/4.2/tinymce.min.js"></script>' . "\n";
    $editor .= '<script type="text/javascript">
    tinymce.init({
        selector: "textarea",
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image' . do_action('myet_wysiwyg_editor_toolbar') . '",
        autosave_ask_before_unload: false
    });
    </script>' . "\n";
    return apply_filter('myet_wysiwyg_editor', $editor);
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
    $php_function = version_compare($latest, $current, $operator);
    /**
     * Filters the comparison between two release.
     *
     * @since 6.1.14
     * @param $php_function PHP
     *            function for comparing two release values.
     */
    $release = apply_filter('compare_releases', $php_function);
    
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
    $headers = get_headers($url);
    $status = substr($headers[0], 9, 3);
    /**
     * Filters the http response code.
     *
     * @since 6.1.14
     * @param
     *            string
     */
    return apply_filter('http_response_code', $status);
}

add_action('admin_head', 'head_release_meta', 5);
add_action('myet_head', 'head_release_meta', 5);
add_action('release', 'foot_release', 5);
add_action('dashboard_top_widgets', 'dashboard_student_count', 5);
add_action('dashboard_top_widgets', 'dashboard_course_count', 5);
add_action('dashboard_top_widgets', 'dashboard_acadProg_count', 5);
add_action('dashboard_right_widgets', 'dashboard_clock', 5);
add_action('dashboard_right_widgets', 'dashboard_weather', 5);
add_filter('the_myet_page_content', 'et_autop');
add_filter('the_myet_page_content', 'parsecode_unautop');
add_filter('the_myet_page_content', 'do_parsecode', 5);
add_filter('the_myet_welcome_message', 'et_autop');
add_filter('the_myet_welcome_message', 'parsecode_unautop');
add_filter('the_myet_welcome_message', 'do_parsecode', 5);

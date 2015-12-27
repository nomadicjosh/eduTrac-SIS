<?php namespace app\src;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Plugin Class for Hook System
 *
 * @license GPLv3
 *         
 * @since 4.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class Plugin
{

    public static $path = '';

    protected static $_app;

    public function __construct(\Liten\Liten $liten = null)
    {
        self::$_app = ! empty($liten) ? $liten : \Liten\Liten::getInstance();
    }

    /**
     * Plugin Basename
     *
     * The method extracts the file name of a specific plugin.
     *
     * @since 4.2.0
     * @param string $filename
     *            Plugin's file name.
     * @return string The file name of the plugin.
     */
    public static function plugin_basename($filename)
    {
        $plugindir = ETSIS_PLUGIN_DIR;
        $dropindir = ETSIS_DROPIN_DIR;
        
        $filename = preg_replace('#^' . preg_quote($plugindir, '#') . '/|^' . preg_quote($dropindir, '#') . '/#', '', $filename);
        $filename = trim($filename, '/');
        return basename($filename);
    }

    /**
     * Register Activation Hook
     *
     * This method is used to run code that should be executed
     * when a plugin is being activated.
     *
     * @since 4.2.0
     * @param string $filename
     *            Plugin's file name.
     * @param string $function
     *            The function which should be executed.
     */
    public static function register_activation_hook($filename, $function)
    {
        $filename = self::plugin_basename($filename);
        add_action('activate_' . $filename, $function);
    }

    /**
     * Register Deactivation Hook
     *
     * This method is used to run code that should be executed
     * when a plugin is being deactivated.
     *
     * @since 6.1.07
     * @param string $filename
     *            Plugin's file name.
     * @param string $function
     *            The function which should be executed.
     */
    public static function register_deactivation_hook($filename, $function)
    {
        $filename = self::plugin_basename($filename);
        add_action('deactivate_' . $filename, $function);
    }

    /**
     * Get the filesystem directory path (with trailing slash) for the plugin __FILE__ passed in.
     *
     * @since 6.2.0
     *       
     * @param string $filename
     *            The filename of the plugin (__FILE__).
     * @return string The filesystem path of the directory that contains the plugin.
     */
    public static function plugin_dir_path($filename)
    {
        return add_trailing_slash(dirname($filename));
    }
}

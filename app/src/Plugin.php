<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Plugin Class for Hook System
 *  
 * @license GPLv3
 * 
 * @since       4.2.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class Plugin
{

    public static $path = '';
    protected static $_app;

    public function __construct(\Liten\Liten $liten = null)
    {
        self::$_app = !empty($liten) ? $liten : \Liten\Liten::getInstance();
    }

    /**
     * Plugin Basename
     * 
     * The method extracts the file name of a specific plugin.
     * 
     * @since 4.2.0
     * @param string $file Plugin's file name.
     * @return string The file name of the plugin.
     */
    public static function plugin_basename($file)
    {
        $plugindir = PLUGINS_DIR;
        $dropindir = DROPINS_DIR;

        $file = preg_replace('#^' . preg_quote($plugindir, '#') . '/|^' . preg_quote($dropindir, '#') . '/#', '', $file);
        $file = trim($file, '/');
        return basename($file);
    }

    /**
     * Register Activation Hook
     * 
     * This method is used to run code that should be executed
     * when a plugin is being activated.
     * 
     * @since 4.2.0
     * @param string $file Plugin's file name.
     * @param string $function The function which should be executed.
     */
    public static function register_activation_hook($file, $function)
    {
        $file = self::plugin_basename($file);
        add_action('activate_' . $file, $function);
    }

    /**
     * Register Deactivation Hook
     * 
     * This method is used to run code that should be executed
     * when a plugin is being deactivated.
     * 
     * @since 6.1.07
     * @param string $file Plugin's file name.
     * @param string $function The function which should be executed.
     */
    public static function register_deactivation_hook($file, $function)
    {
        $file = self::plugin_basename($file);
        add_action('deactivate_' . $file, $function);
    }
}

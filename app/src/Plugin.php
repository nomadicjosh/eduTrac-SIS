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

    public $path = '';

    public $app;
    
    /**
     *
     * @var Singleton
     */
    protected static $instance;

    public function __construct(\Liten\Liten $liten = null)
    {
        $this->app = ! empty($liten) ? $liten : \Liten\Liten::getInstance();
    }
    
    public static function inst()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
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
    public function plugin_basename($filename)
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
    public function register_activation_hook($filename, $function)
    {
        $filename = $this->plugin_basename($filename);
        $this->app->hook->add_action('activate_' . $filename, $function);
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
    public function register_deactivation_hook($filename, $function)
    {
        $filename = $this->plugin_basename($filename);
        $this->app->hook->add_action('deactivate_' . $filename, $function);
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
    public function plugin_dir_path($filename)
    {
        return add_trailing_slash(dirname($filename));
    }
}

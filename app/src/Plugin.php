<?php namespace app\srcs;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Plugin Class for Hook System
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @license     http://www.edutracerp.com/general/edutrac-erp-commercial-license/ Commercial License
 * @link        http://www.7mediaws.org/
 * @since       4.2.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
class Plugin
{

    public static $path = '';
    protected static $_app;

    public function __construct(\Liten\Liten $liten = null)
    {
        self::$_app = !empty($liten) ? $liten : \Liten\Liten::getInstance();
    }

    public static function plugin_basename($file)
    {
        foreach (self::$path as $dir => $realdir) {
            if (strpos($file, $realdir) === 0) {
                $file = $dir . substr($file, strlen($realdir));
            }
        }
        $plugindir = PLUGINS_DIR;
        $dropindir = DROPINS_DIR;

        $file = preg_replace('#^' . preg_quote($plugindir, '#') . '/|^' . preg_quote($dropindir, '#') . '/#', '', $file);
        $file = trim($file, '/');
        return $file;
    }

    public static function register_activation_hook($file, $function)
    {
        $file = self::plugin_basename($file);
        self::$_app->hook->add_action('activate_' . $file, $function);
    }
}

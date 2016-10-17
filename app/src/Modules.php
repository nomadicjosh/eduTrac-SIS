<?php namespace app\src;

use app\src\Core\Exception\NotFoundException;

/**
 * Modules Library
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : College Management System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       5.0
 * @package     eduTrac ERP
 * @author      Joshua Parker <josh@7mediaws.org>
 */
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

class Modules
{

    /**
     * @access public
     * @var object
     *
     */
    public $app;

    /**
     * @access public
     * @var string
     *
     */
    public $modules_dir;

    /**
     * all plugins header information in an array.
     * 
     * @access public
     * @var array
     */
    public $modules_header = [];

    /**
     * __construct class constructor
     * 
     * @access public
     * @since 1.0.1
     */
    public function __construct(\Liten\Liten $liten = null)
    {
        $this->app = !empty($liten) ? $liten : \Liten\Liten::getInstance();
    }

    /**
     * Returns the module header information
     *
     * @access public
     * @since 1.0.1
     * @param string (optional) $modules_dir Loads modules from specified folder
     * @return mixed
     *
     */
    public function get_modules_header($modules_dir = '')
    {

        if ($handle = opendir($modules_dir)) {

            while ($file = readdir($handle)) {
                if (is_file($modules_dir . $file)) {
                    if (strpos($modules_dir . $file, '.module.php')) {
                        $fp = fopen($modules_dir . $file, 'r');
                        // Pull only the first 8kiB of the file in.
                        $module_data = fread($fp, 8192);
                        fclose($fp);

                        preg_match('|Module Name:(.*)$|mi', $module_data, $name);
                        preg_match('|Module URI:(.*)$|mi', $module_data, $uri);
                        preg_match('|Version:(.*)|i', $module_data, $version);
                        preg_match('|Description:(.*)$|mi', $module_data, $description);
                        preg_match('|Author:(.*)$|mi', $module_data, $author_name);
                        preg_match('|Author URI:(.*)$|mi', $module_data, $author_uri);
                        preg_match('|Module Slug:(.*)$|mi', $module_data, $module_slug);

                        foreach (array('name', 'uri', 'version', 'description', 'author_name', 'author_uri', 'module_slug') as $field) {
                            if (!empty(${$field}))
                                ${$field} = trim(${$field} [1]);
                            else
                                ${$field} = '';
                        }
                        $module_data = array('filename' => $file, 'Name' => $name, 'Title' => $name, 'ModuleURI' => $uri, 'Description' => $description, 'Author' => $author_name, 'AuthorURI' => $author_uri, 'Version' => $version);
                        $this->modules_header [] = $module_data;
                    }
                } else if ((is_dir($modules_dir . $file)) && ($file != '.') && ($file != '..')) {
                    $this->get_modules_header($modules_dir . $file . '/');
                }
            }

            closedir($handle);
        }
        return $this->modules_header;
    }

    /**
     * Loads all installed modules for inclusion.
     * 
     * @access public
     * @since 1.0.1
     * @param string (optional) $modules_dir Loads modules from specified folder
     * @return mixed
     */
    public function load_installed_modules($modules_dir = '')
    {
        $modules = glob(APP_PATH . 'modules' . DS . '*.module.php');
        if (is_array($modules)) {
            foreach ($modules as $module) {
                try {
                    if (etsis_file_exists($module)) {
                        require_once($module);
                    }
                } catch (NotFoundException $e) {
                    \Cascade\Cascade::getLogger('error')->error(sprintf('FILESTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                }
            }
        }
    }
}

<?php namespace app\src;
if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Database Cache Class
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       4.3
 * @package     eduTrac ERP
 * @author      Joshua Parker <josh@7mediaws.org>
 */

defined( 'CACHE_PATH' )	or define( 'CACHE_PATH', '/tmp/' );

class DBCache {
    
	/**
	 * The path to the cache file folder
	 *
     * @access private
     * @since 4.3
	 * @var string
	 */
	private $_cachepath = CACHE_PATH;
	
	/**
	 * The key name of the cache file
	 *
     * @access private
     * @since 4.3
	 * @var string
	 */
	private $_cachename = 'default';
	
	/**
	 * The cache file extension
	 *
     * @access private
     * @since 4.3
	 * @var string
	 */
	private $_extension = '.db';
	
	/**
	 * Time to live for cache file
	 *
     * @access private
     * @since 4.3
	 * @var int
	 */
	private $_setTTL = '2592000';
	
	/**
	 * Full location of cache file
	 *
     * @access private
     * @since 4.3
	 * @var string
	 */
	private $_cachefile;
	
	/**
	 * Execution Time
	 *
     * @access private
     * @since 4.3
	 * @var float
	 */
	private $_starttime;
	
	/**
	 * Logs errors that may occur
	 *
     * @access private
     * @since 4.3
	 * @var float
	 */
	private $_log;
    
    protected $_app;
	
	public function __construct() {		
		if(!is_dir($this->_cachepath) || !is_writeable($this->_cachepath)) mkdir($this->_cachepath, 0755);
        $this->_app = \Liten\Liten::getInstance();
		
		$mtime = microtime();
   		$mtime = explode(" ",$mtime);
   		$mtime = $mtime[1] + $mtime[0];
   		$this->_starttime = $mtime;
	}
	
	/**
	 * Reads a cache file if it exists and prints it out 
	 * to the screen.
	 * 
     * @access public
     * @since 4.3
	 * @param string (required) $filename Full path to the requested cache file
	 * @return mixed
	 */
	public function getCache($fileName) {
		$this->_cachefile = $this->_cachepath . md5($fileName.\app\src\Campus::check(0)) . $this->_extension;
		if ( file_exists($this->_cachefile) ) {
			$cache = fopen($this->_cachefile, 'rb');
			$output = fread($cache, filesize($this->_cachefile));
			fclose($cache);
			return $this->_app->hook->maybe_unserialize($output);
		} else {
			return $this->addLog( 'Could not find filename: ' . md5($fileName.\app\src\Campus::check(0)) );
		}
	}
	
	/**
	 * Writes cache data to be read
	 * 
     * @access public
     * @since 4.3
	 * @param string (required) $data Data that should be cached
	 * @param string (required) $filename Name of the cache file
	 * @return mixed
	 */
	public function writeCache($fileName, $data) {
		if(_h($this->_app->hook->{'get_option'}( 'db_caching' )) == 1) {
			$this->_cachefile = $this->_cachepath . md5($fileName.\app\src\Campus::check(0)) . $this->_extension;
			$fp = fopen($this->_cachefile, 'w');
			if($fp) {
				$values = $this->_app->hook->maybe_serialize($data);
		    	fwrite($fp, $values);
		    	fclose($fp);
			} else {
				return $this->addLog( 'Could not read filename: ' . md5($fileName.\app\src\Campus::check(0)) . ' data: ' . $data );
			}
		}
	}
	
	/**
	 * Checks if a cache file is valid
	 * 
     * @access public
     * @since 4.3
	 * @param string (required) $filename Name of the cache file
	 * @return mixed
	 */
	public function isCacheValid($fileName) {
		$this->_cachefile = $this->_cachepath . $fileName . $this->_extension;
		if(file_exists($this->_cachefile) && (filemtime($this->_cachefile) > (time() - $this->_setTTL))){
			return true;
		}else{
			return $this->addLog( 'Could not find filename: ' . $fileName );	
		}
	}
	
	/**
	 * Clears the cache base on cache file name/key
	 * 
     * @access public
     * @since 4.3
	 * @param string (required) $filename Key name of cache
	 * @return mixed
	 */
	public function clearCache($fileName) {
		$cachelog = $this->_cachepath . md5($fileName.\app\src\Campus::check(0)) . $this->_extension;
		if(file_exists($cachelog)) {
			unlink($cachelog);
		}
	}
	
	/**
	 * Clears all cache files
	 * 
     * @access public
     * @since 4.3
	 * @return mixed
	 */
	public function purge() {
		foreach(glob($this->_cachepath . '*.db') as $file) {
			unlink($file);
		}
	}
	
	/**
	 * Prints a log if error occurs
	 * 
     * @access public
     * @since 4.3
	 * @param mixed (required) $value Message that should be returned
	 * @return mixed
	 */
	public function addLog($value) {
        $this->_log = [];
		array_push($this->_log, round((microtime(true) - $this->_starttime),5).'s - '. $value);
	}
}
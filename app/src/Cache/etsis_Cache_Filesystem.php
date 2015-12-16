<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * eduTrac SIS Filesystem Cache Class.
 *
 * @license GPLv3
 *         
 * @since 6.2.00
 * @package eduTrac SIS
 * @subpackage Cache
 * @author Joshua Parker <joshmac3@icloud.com>
 */

class etsis_Cache_Filesystem extends \app\src\Cache\etsis_Abstract_cache
{

    /**
     * Application object.
     *
     * @var object
     */
    protected $_app;

    /**
     * Cache directory object.
     *
     * @var string
     */
    protected $_dir;

    public function __construct(\Liten\Liten $liten = null)
    {
        $this->_app = ! empty($liten) ? $liten : \Liten\Liten::getInstance();
        
        /**
         * File system cache directory.
         */
        $dir = $this->_app->config('file.savepath') . 'cache';
        
        /**
         * Fiter the file cache directory in order to override it
         * in case some systems are having issues.
         *
         * @since 6.2.00
         * @param string $dir
         *            The directory where file system cache files are saved.
         */
        $cacheDir = apply_filter('filesystem_cache_dir', $dir);
        
        /**
         * If the cache directory does not exist, the create it first
         * before trying to call it for use.
         */
        if (! is_dir($cacheDir) || ! file_exists($cacheDir)) {
            _mkdir($cacheDir);
        }
        
        /**
         * Cache directory is set.
         */
        $this->_dir = $cacheDir . DS;
        
        return true;
    }

    /**
     * Creates the cache file.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param int $ttl
     *            Time to live sets the life of the cache file.
     */
    public function create($key, $data, $ttl)
    {
        // Opening the file in read/write mode
        $h = fopen($this->getFileName($key), 'a+');
        
        // If there is an issue with the handler, throw and exception.
        if (! $h) {
            return new \app\src\Exception\Exception(_t('Could not write to cache'), 'file_system_cache');
        }
        
        // exclusive lock, will get released when the file is closed
        flock($h, LOCK_EX);
        
        // go to the start of the file
        fseek($h, 0);
        
        // truncate the file
        ftruncate($h, 0);
        
        // Serializing along with the TTL
        $data = maybe_serialize(array(
            time() + (int) $ttl,
            $data
        ));
        if (fwrite($h, $data) === false) {
            return new \app\src\Exception\Exception(_t('Could not write to cache'), 'file_system_cache');
        }
        fclose($h);
    }

    /**
     * Fetches cached data.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the cache file.
     */
    public function read($key)
    {
        $file_name = $this->getFileName($key);
        if (! file_exists($file_name)) {
            return false;
        }
        
        $h = fopen($file_name, 'r');
        
        if (! $h) {
            return false;
        }
        
        // Getting a shared lock
        flock($h, LOCK_SH);
        
        $data = file_get_contents($file_name);
        fclose($h);
        
        $data = maybe_unserialize($data);
        if (! $data) {
            
            // If unserializing somehow didn't work out, we'll delete the file
            unlink($file_name);
            return false;
        }
        
        if (time() > $data[0]) {
            
            // Unlinking when the file has expired
            unlink($file_name);
            return false;
        }
        return $data[1];
    }

    /**
     * Updates a cache file based on unique ID.
     * This method only exists for
     * CRUD completeness purposes and just basically calls the create method.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param int $ttl
     *            Time to live sets the life of the cache file.
     */
    public function update($key, $data, $ttl)
    {
        return $this->create($key, $data, $ttl);
    }

    /**
     * Deletes a cache file based on unique key.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of cache file.
     */
    public function delete($key)
    {
        $file_name = $this->getFileName($key);
        if (file_exists($file_name)) {
            return unlink($file_name);
        } else {
            return false;
        }
    }

    /**
     * Flushes the file system cache completely.
     *
     * @since 6.2.00
     */
    public function flush()
    {
        $cache = glob($this->_dir . 's_*');
        if (is_array($cache)) {
            foreach ($cache as $file_name) {
                if (file_exists($file_name))
                    return unlink($file_name);
            }
        }
        
        return true;
    }

    private function getFileName($key)
    {
        return $this->_dir . 's_cache' . md5($key);
    }
}

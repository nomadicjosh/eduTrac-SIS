<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Cookie Cache Class.
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @subpackage Cache
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Cache_Cookie extends \app\src\Cache\etsis_Abstract_cache
{

    /**
     * Application object.
     *
     * @since 6.2.0
     * @var object
     */
    protected $_app;

    /**
     * Cache directory object.
     *
     * @since 6.2.0
     * @var string
     */
    protected $_dir;

    /**
     * Holds the cached objects.
     *
     * @since 6.2.0
     * @var array
     */
    protected $_cache = [];

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
         * @since 6.2.0
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
         * If the directory isn't writable, throw an exception.
         */
        if (! etsis_is_writable($cacheDir)) {
            return new \app\src\Exception\Exception(_t('Could not create the file cache directory.'), 'cookie_cache');
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
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::create()
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $namespace
     *            Optional. Where to namespace the cache contents. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: 0 = expires immediately after request.
     */
    public function create($key, $data, $namespace = 'default', $ttl = 0)
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        $unique_key = $this->uniqueKey($key, $namespace);
        
        if ($this->_exists($unique_key, $namespace)) {
            return false;
        }
        
        // Opening the file in read/write mode
        $h = fopen($this->getFileName($unique_key), 'a+');
        
        // If there is an issue with the handler, throw and exception.
        if (! $h) {
            return new \app\src\Exception\Exception(_t('Could not write to cache'), 'cookie_cache');
        }
        
        $this->_app->cookies->set(md5($unique_key), $unique_key, $ttl);
        
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
            return new \app\src\Exception\Exception(_t('Could not write to cache'), 'cookie_cache');
        }
        fclose($h);
    }

    /**
     * Fetches cached data.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::read()
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the cache file.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     */
    public function read($key, $namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        $unique_key = $this->uniqueKey($key, $namespace);
        
        $x = isset($_COOKIE[md5($unique_key)]) ? $_COOKIE[md5($unique_key)] : false;
        if ($x == false) {
            return null;
        } else {
            $file_name = $this->getFileName($unique_key);
        }
        
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
            $this->_app->cookies->remove(md5($unique_key));
            unlink($file_name);
            return false;
        }
        
        if (time() > $data[0]) {
            
            // Unlinking when the file has expired
            $this->_app->cookies->remove(md5($unique_key));
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
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::update()
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $namespace
     *            Optional. Where to namespace the cache contents. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: no expiration.
     */
    public function update($key, $data, $namespace = 'default', $ttl = 0)
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        return $this->create($key, $data, $namespace, $ttl);
    }

    /**
     * Deletes a cache file based on unique key.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::delete()
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of cache file.
     * @param string $namespace
     *            Optional. Where to namespace the cache contents. Default: 'default'.
     */
    public function delete($key, $namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        $unique_key = $this->uniqueKey($key, $namespace);
        
        if (! $this->_exists($unique_key, $namespace)) {
            return false;
        }
        
        $file_name = $this->getFileName($unique_key);
        if (file_exists($file_name)) {
            $this->_app->cookies->remove(md5($unique_key));
            return unlink($file_name);
        } else {
            return false;
        }
    }

    /**
     * Flushes the file system cache completely.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::flush()
     *
     * @since 6.2.0
     */
    public function flush()
    {
        $cache = glob($this->_dir . 's_cache*');
        if (is_array($cache)) {
            foreach ($cache as $file_name) {
                if (file_exists($file_name)) {
                    $key = str_replace($this->_dir . 's_cache', '', $file_name);
                    $this->_app->cookies->remove($key);
                    unlink($file_name);
                }
            }
        }
        
        return true;
    }

    /**
     * Removes all cache items from a particular namespace.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::flushNamespace()
     *
     * @since 6.2.0
     * @param int|string $namespace
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     */
    public function flushNamespace($namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        $cache = glob($this->_dir . 's_cache_' . $namespace . ':*');
        if (is_array($cache)) {
            foreach ($cache as $file_name) {
                if (file_exists($file_name)) {
                    $key = str_replace($this->_dir . 's_cache_' . $namespace, '', $file_name);
                    $this->_app->cookies->remove($key);
                    unlink($file_name);
                }
            }
        }
        
        return true;
    }

    /**
     * Generates a unique cache key.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::uniqueKey()
     *
     * @since 6.2.0
     * @access protected
     * @param int|string $key
     *            Unique key for cache file.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     */
    protected function uniqueKey($key, $namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        return $this->_cache[$namespace][$key] = $namespace . ':' . $key;
    }

    /**
     * Serves as a utility method to determine whether a key exists in the cache.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::_exists()
     *
     * @since 6.2.0
     * @access protected
     * @param int|string $key
     *            Cache key to check for existence.
     * @param string $namespace
     *            Cache namespace for the key existence check.
     * @return bool Whether the key exists in the cache for the given namespace.
     */
    protected function _exists($key, $namespace)
    {
        return isset($this->_cache[$namespace]) && (isset($this->_cache[$namespace][$key]) || array_key_exists($key, $this->_cache[$namespace]));
    }

    /**
     * Unique namespace for cache item.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::_namespace()
     *
     * @since 6.2.0
     * @param int|string $value
     *            The value to slice to get namespace.
     */
    protected function _namespace($value)
    {
        $namespace = explode(':', $value);
        return $namespace[0] . ':';
    }

    /**
     * Retrieve the cache file.
     *
     * @since 6.2.0
     * @access protected
     * @param int|string $key
     *            Unqiue key of cache.
     */
    private function getFileName($key)
    {
        return $this->_dir . 's_cache_' . $this->_namespace($key) . md5($key);
    }
}

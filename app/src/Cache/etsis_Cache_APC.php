<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS APC Cache Class.
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @subpackage Cache
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Cache_APC extends \app\src\Cache\etsis_Abstract_cache
{
    /**
     * Holds the cached objects.
     *
     * @since 6.2.0
     * @var array
     */
    protected $_cache = [];

    public function __construct()
    {
        if (! extension_loaded('apc') && ! ini_get('apc.enabled') || !function_exists('apc_fetch')) {
            return new \app\src\Exception\Exception(_t('APC requires PHP APC extension to be installed and loaded.'), 'php_apc_extension');
        }
        return true;
    }

    /**
     * Creates the APC cache item.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the APC cached item.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the APC cached item. Default: 0 = will persist until cleared.
     */
    public function create($key, $data, $group = 'default', $ttl = 0)
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        $unique_key = $this->unique_key($key, $group);
        
        if ($this->_exists($unique_key, $group)) {
            return false;
        }
        
        return apc_store($unique_key, $data, $ttl);
    }

    /**
     * Fetches cached data.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the APC cached item.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     */
    public function read($key, $group = 'default')
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        $unique_key = $this->unique_key($key, $group);
        
        return apc_fetch($unique_key);
    }

    /**
     * Updates the APC cache based on unique key.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the APC cache.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the APC cached item. Default: 0 = will persist until cleared.
     */
    public function update($key, $data, $group = 'default', $ttl = 0)
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        $unique_key = $this->unique_key($key, $group);
        
        return apc_store($unique_key, $data, $ttl);
    }

    /**
     * Deletes cache based on unique key.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of APC cache.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     */
    public function delete($key, $group = 'default')
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        $unique_key = $this->unique_key($key, $group);
        
        return apc_delete($unique_key);
    }

    /**
     * Flushes the APC cache completely.
     *
     * @since 6.2.0
     */
    public function flush()
    {
        apc_clear_cache();
        apc_clear_cache('user');
    }
    
    /**
     * Generates a unique cache key.
     *
     * @since 6.2.0
     * @access protected
     * @param int|string $key
     *            Unique key for cache file.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     */
    protected function unique_key($key, $group = 'default')
    {
        if (empty($group)) {
            $group = 'default';
        }
    
        return $this->_cache[$group][$key] = $group . ':' . $key;
    }
    
    /**
     * Serves as a utility method to determine whether a key exists in the cache.
     *
     * @since 6.2.0
     * @access protected
     * @param int|string $key
     *            Cache key to check for existence.
     * @param string $group
     *            Cache group for the key existence check.
     * @return bool Whether the key exists in the cache for the given group.
     */
    protected function _exists($key, $group)
    {
        return isset($this->_cache[$group]) && (isset($this->_cache[$group][$key]) || array_key_exists($key, $this->_cache[$group]));
    }
}

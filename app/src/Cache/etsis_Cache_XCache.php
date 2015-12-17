<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS XCache Cache Class.
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @subpackage Cache
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Cache_XCache extends \app\src\Cache\etsis_Abstract_cache
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
        if (! extension_loaded('xcache') && ! function_exists('xcache_get')) {
            return new \app\src\Exception\Exception(_t('XCache requires PHP XCache extension to be installed and loaded.'), 'php_xcache_extension');
        }
        return true;
    }

    /**
     * Creates the cache file.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: 0 = will persist until cleared.
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
        
        return xcache_set($unique_key, $data, $ttl);
    }

    /**
     * Fetches cached data.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the cache file.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     */
    public function read($key, $group = 'default')
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        $unique_key = $this->unique_key($key, $group);
        
        return xcache_isset($unique_key) ? xcache_get($unique_key) : false;
    }

    /**
     * Updates a cache file based on unique ID.
     * This method only exists for
     * CRUD completeness purposes and just basically calls the create method.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: 0 = will persist until cleared.
     */
    public function update($key, $data, $group = 'default', $ttl = 0)
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        $unique_key = $this->unique_key($key, $group);
        
        return $this->create($unique_key, $data, $ttl);
    }

    /**
     * Deletes a cache file based on unique key.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of cache file.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     */
    public function delete($key, $group = 'default')
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        $unique_key = $this->unique_key($key, $group);
        
        return xcache_unset($unique_key);
    }

    /**
     * Flushes the cache completely.
     *
     * @since 6.2.0
     */
    public function flush()
    {
        for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i ++) {
            if (xcache_clear_cache(XC_TYPE_VAR, $i) === false) {
                return false;
            }
        }
        return true;
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

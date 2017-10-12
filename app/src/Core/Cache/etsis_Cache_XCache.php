<?php namespace app\src\Core\Cache;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\Exception;

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
class etsis_Cache_XCache extends \app\src\Core\Cache\etsis_Abstract_Cache
{

    /**
     * Holds the cached objects.
     *
     * @since 6.2.0
     * @var array
     */
    protected $_cache = [];

    /**
     * Holds the application object
     *
     * @since 6.2.0
     * @var object
     */
    public $app;

    /**
     * Sets if cache is enabled or not.
     *
     * @since 6.2.0
     * @var bool
     */
    public $enable;

    public function __construct(\Liten\Liten $liten = null)
    {
        $this->app = !empty($liten) ? $liten : \Liten\Liten::getInstance();

        if (!extension_loaded('xcache') && !function_exists('xcache_get')) {
            throw new Exception(_t('XCache requires PHP XCache extension to be installed and loaded.'), 'php_xcache_extension');
        }

        /**
         * Filter sets whether caching is enabled or not.
         *
         * @since 6.2.0
         * @var bool
         */
        $this->enable = $this->app->hook->apply_filter('enable_caching', true);
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
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: 0 = will persist until cleared.
     */
    public function create($key, $data, $namespace = 'default', $ttl = 0)
    {
        if (!$this->enable) {
            return false;
        }

        if (empty($namespace)) {
            $namespace = 'default';
        }

        return $this->set($key, $data, $namespace, (int) $ttl);
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
        if (!$this->enable) {
            return false;
        }

        if (empty($namespace)) {
            $namespace = 'default';
        }

        $unique_key = $this->uniqueKey($key, $namespace);

        return xcache_isset($unique_key) ? xcache_get($unique_key) : false;
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
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: 0 = will persist until cleared.
     */
    public function update($key, $data, $namespace = 'default', $ttl = 0)
    {
        if (!$this->enable) {
            return false;
        }

        if (empty($namespace)) {
            $namespace = 'default';
        }

        $unique_key = $this->uniqueKey($key, $namespace);

        return $this->create($unique_key, $data, (int) $ttl);
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
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     */
    public function delete($key, $namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }

        $unique_key = $this->uniqueKey($key, $namespace);

        return xcache_unset($unique_key);
    }

    /**
     * Flushes the cache completely.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::flush()
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

        return xcache_inc($namespace, 10);
    }

    /**
     * Sets the data contents into the cache.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::set()
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: 0 = expires immediately after request.
     */
    public function set($key, $data, $namespace = 'default', $ttl = 0)
    {
        if (!$this->enable) {
            return false;
        }

        if (empty($namespace)) {
            $namespace = 'default';
        }

        $unique_key = $this->uniqueKey($key, $namespace);

        if ($this->_exists($unique_key, $namespace)) {
            return false;
        }

        return xcache_set($unique_key, $data, (int) $ttl);
    }

    /**
     * Echoes the stats of the cache.
     *
     * Gives the cache hits, cache misses and cache uptime.
     *
     * @since 6.2.0
     */
    public function getStats()
    {
        if (!$this->enable) {
            return false;
        }

        $info = xcache_info(XC_TYPE_VAR, 0);

        echo "<p>";
        echo "<strong>" . _t('Cache Hits:') . "</strong> " . $info['hits'] . "<br />";
        echo "<strong>" . _t('Cache Misses:') . "</strong> " . $info['misses'] . "<br />";
        echo "<strong>" . _t('Uptime:') . "</strong> " . null . "<br />";
        echo "<strong>" . _t('Memory Usage:') . "</strong> " . $info['size'] . "<br />";
        echo "<strong>" . _t('Memory Available:') . "</strong> " . $info['avail'] . "<br />";
        echo "</p>";
    }

    /**
     * Increments numeric cache item's value.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::inc()
     *
     * @since 6.2.0
     * @param int|string $key
     *            The cache key to increment
     * @param int $offset
     *            Optional. The amount by which to increment the item's value. Default: 1.
     * @param string $namespace
     *            Optional. The namespace the key is in. Default: 'default'.
     * @return false|int False on failure, the item's new value on success.
     */
    public function inc($key, $offset = 1, $namespace = 'default')
    {
        $unique_key = $this->uniqueKey($key, $namespace);

        return xcache_inc($unique_key, (int) $offset);
    }

    /**
     * Decrements numeric cache item's value.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::dec()
     *
     * @since 6.2.0
     * @param int|string $key
     *            The cache key to decrement.
     * @param int $offset
     *            Optional. The amount by which to decrement the item's value. Default: 1.
     * @param string $namespace
     *            Optional. The namespace the key is in. Default: 'default'.
     * @return false|int False on failure, the item's new value on success.
     */
    public function dec($key, $offset = 1, $namespace = 'default')
    {
        $unique_key = $this->uniqueKey($key, $namespace);

        return xcache_dec($unique_key, (int) $offset);
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
}

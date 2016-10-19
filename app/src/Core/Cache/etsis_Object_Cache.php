<?php namespace app\src\Core\Cache;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Object Cache Class
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @subpackage Cache
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Object_Cache
{

    /**
     * The cache object.
     *
     * @since 6.2.0
     * @access protected
     * @var object
     */
    protected $_cache;

    /**
     * Application global scope.
     * 
     * @since 6.2.0
     * @access public
     * @var object
     */
    public $app;

    public function __construct($driver, \Liten\Liten $liten = null)
    {
        $this->app = !empty($liten) ? $liten : \Liten\Liten::getInstance();

        if ($driver == 'file') {
            $this->_cache = new \app\src\Core\Cache\etsis_Cache_Filesystem();
        } elseif ($driver == 'apc') {
            $this->_cache = new \app\src\Core\Cache\etsis_Cache_APC();
        } elseif ($driver == 'memcache') {
            /**
             * Filter whether to use `\Memcache`|`\Memcached`.
             *
             * @since 6.2.0
             * @param
             *            bool false Use `\Memcache`|`\Memcached`. Default is false.
             */
            $useMemcached = $this->app->hook->apply_filter('use_memcached', false);

            $this->_cache = new \app\src\Core\Cache\etsis_Cache_Memcache($useMemcached);

            $pool = [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 20
                ]
            ];
            /**
             * Filter the `\Memcache`|`\Memcached` server pool.
             *
             * @since 6.2.0
             * @param array $pool
             *            Array of servers to add to the connection pool.
             */
            $servers = $this->app->hook->apply_filter('memcache_server_pool', $pool);

            $this->_cache->addServer($servers);
        } elseif ($driver == 'external') {
            /**
             * Fires when being used to call another caching system not
             * native to eduTrac SIS.
             *
             * @since 6.2.0
             */
            $this->_cache = $this->app->hook->do_action('external_cache_driver');
        } elseif ($driver == 'xcache') {
            $this->_cache = new \app\src\Core\Cache\etsis_Cache_XCache();
        } elseif ($driver == 'cookie') {
            $this->_cache = new \app\src\Core\Cache\etsis_Cache_Cookie();
        } elseif ($driver == 'json') {
            $this->_cache = new \app\src\Core\Cache\etsis_Cache_JSON();
        } elseif ($driver == 'memory') {
            $this->_cache = new \app\src\Core\Cache\etsis_Cache_Memory();
        }

        $error = $this->_cache;
        if (is_etsis_exception($error)) {
            Cascade\Cascade::getLogger('error')->error(sprintf('IOSTATE[%s]: Forbidden: %s', $error->getCode(), $error->getMessage()));
            return false;
        }

        return $this->_cache;
    }

    /**
     * Creates the cache file.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced.
     * @param int $ttl
     *            Time to live sets the life of the cache file.
     */
    public function create($key, $data, $namespace = 'default', $ttl = 0)
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }

        return $this->_cache->create($key, $data, $namespace, (int) $ttl);
    }

    /**
     * Fetches cached data.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the cache file.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced.
     */
    public function read($key, $namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }

        return $this->_cache->read($key, $namespace);
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
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced.
     * @param int $ttl
     *            Time to live sets the life of the cache file.
     */
    public function update($key, $data, $namespace = 'default', $ttl = 0)
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }

        return $this->create($key, $data, $namespace, (int) $ttl);
    }

    /**
     * Deletes a cache file based on unique key.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of cache file.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced.
     */
    public function delete($key, $namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }

        return $this->_cache->delete($key, $namespace);
    }

    /**
     * Flushes the file system cache completely.
     *
     * @since 6.2.0
     */
    public function flush()
    {
        return $this->_cache->flush();
    }

    /**
     * Removes all cache items from a particular namespace.
     *
     * @since 6.2.0
     * @param int|string $namespace
     *            Optional. Where the cache contents are namespaced.
     */
    public function flushNamespace($namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }

        return $this->_cache->flushNamespace($namespace);
    }

    /**
     * Sets the data contents into the cache.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: 0 = expires immediately after request.
     * @return bool Returns true if the cache was set and false otherwise.
     */
    public function set($key, $data, $namespace = 'default', $ttl = 0)
    {
        try {
            if (empty($namespace)) {
                $namespace = 'default';
            }
            return $this->_cache->set($key, $data, $namespace, (int) $ttl);
        } catch (\app\src\Core\Exception\IOException $e) {
            Cascade::getLogger('error')->error(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()));
        }
    }

    /**
     * Returns stats of the cache.
     *
     * Gives the cache hits, cache misses and cache uptime.
     *
     * @since 6.2.0
     */
    public function getStats()
    {
        return $this->_cache->getStats();
    }

    /**
     * Increments numeric cache item's value.
     *
     * @since 6.2.0
     * @param int|string $key
     *            The cache key to increment
     * @param int $offset
     *            Optional. The amount by which to increment the item's value.
     * @param string $namespace
     *            Optional. The namespace the key is in.
     * @return false|int False on failure, the item's new value on success.
     */
    public function inc($key, $offset = 1, $namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }

        return $this->_cache->inc($key, (int) $offset, $namespace);
    }

    /**
     * Decrements numeric cache item's value.
     *
     * @since 6.2.0
     * @param int|string $key
     *            The cache key to decrement.
     * @param int $offset
     *            Optional. The amount by which to decrement the item's value.
     * @param string $namespace
     *            Optional. The namespace the key is in.
     * @return false|int False on failure, the item's new value on success.
     */
    public function dec($key, $offset = 1, $namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }

        return $this->_cache->dec($key, (int) $offset, $namespace);
    }
}

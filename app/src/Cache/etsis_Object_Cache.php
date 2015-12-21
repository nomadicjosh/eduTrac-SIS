<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
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

    public function __construct($type)
    {
        if ($type == 'file') {
            $this->_cache = new \app\src\Cache\etsis_Cache_Filesystem();
        } elseif ($type == 'apc') {
            $this->_cache = new \app\src\Cache\etsis_Cache_APC();
        } elseif ($type == 'memcache') {
            /**
             * Filter whether to use \Memcache|\Memcached.
             *
             * @since 6.2.0
             * @param
             *            bool false Use \Memcache|\Memcached. Default is false.
             */
            $useMemcached = apply_filter('use_memcached', false);
            
            $this->_cache = new \app\src\Cache\etsis_Cache_Memcache($useMemcached);
            
            $pool = [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 20
                ]
            ];
            /**
             * Filter the \Memcache|\Memcached server pool.
             *
             * @since 6.2.0
             * @param array $pool
             *            Array of servers to add to the connection pool.
             */
            $servers = apply_filter('memcache_server_pool', $pool);
            
            $this->_cache->addServer($servers);
        } elseif ($type == 'custom') {
            /**
             * Fires when being used to call another caching system not
             * native to eduTrac SIS.
             *
             * @since 6.2.0
             */
            $this->_cache = do_action('custom_cache_system');
        } elseif ($type == 'xcache') {
            $this->_cache = new \app\src\Cache\etsis_Cache_XCache();
        } elseif ($type == 'cookie') {
            $this->_cache = new \app\src\Cache\etsis_Cache_Cookie();
        } elseif ($type == 'json') {
            $this->_cache = new \app\src\Cache\etsis_Cache_JSON();
        }
        
        if (is_et_exception($this->_cache)) {
            return $this->_cache->getMessage();
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
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: 0.
     */
    public function create($key, $data, $namespace = 'default', $ttl = 0)
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        return $this->_cache->create($key, $data, $namespace, $ttl);
    }

    /**
     * Fetches cached data.
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
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: 0.
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
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     */
    public function flushNamespace($namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        return $this->_cache->flushNamespace($namespace);
    }
}

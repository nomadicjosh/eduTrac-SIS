<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Cache Class
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @subpackage Cache
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class Object_Cache
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
            $this->_cache = do_action('custom_cache_system');
        } elseif ($type == 'xcache') {
            $this->_cache = new \app\src\Cache\etsis_Cache_XCache();
        } elseif ($type == 'cookie') {
            $this->_cache = new \app\src\Cache\etsis_Cache_Cookie();
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
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the cache file. Default: 0.
     */
    public function create($key, $data, $group = 'default', $ttl = 0)
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        return $this->_cache->create($key, $data, $group, $ttl);
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
        
        return $this->_cache->read($key, $group);
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
     *            Time to live sets the life of the cache file. Default: 0.
     */
    public function update($key, $data, $group = 'default', $ttl = 0)
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        return $this->create($key, $data, $group, $ttl);
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
        
        return $this->_cache->delete($key, $group);
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
}

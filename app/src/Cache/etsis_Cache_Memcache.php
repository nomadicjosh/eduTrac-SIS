<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS \Memcache|\Memcached Class.
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @subpackage Cache
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Cache_Memcache extends \app\src\Cache\etsis_Abstract_cache
{

    /**
     * \Memcache|\Memcached object;
     *
     * @var object;
     */
    public $connection;

    /**
     *
     * @var bool
     */
    public $useMemcached;

    /**
     * Holds the cached objects.
     *
     * @since 6.2.0
     * @var array
     */
    protected $_cache = [];

    public function __construct($useMemcached)
    {
        $this->useMemcached = $useMemcached;
        
        $ext = $this->useMemcached ? 'memcached' : 'memcache';
        
        if ($ext == 'memcached' && ! class_exists('memcached')) {
            return new \app\src\Exception\Exception(sprintf(_t('Memcached requires PHP <strong>%s</strong> extension to be loaded.'), $ext), 'php_memcache_extension');
        }
        
        if ($ext == 'memcache' && ! function_exists('memcache_connect')) {
            return new \app\src\Exception\Exception(sprintf(_t('Memcached requires PHP <strong>%s</strong> extension to be loaded.'), $ext), 'php_memcache_extension');
        }
        
        if ($ext == 'memcache') {
            $this->connection = new \MemCache();
        } else {
            $this->connection = new \MemCached('etsis');
        }
    }

    /**
     * Creates the \Memcache|\Memcached item.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the \Memcache|\Memcached item.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the \Memcache|\Memcached item. Default: 0 = will persist until cleared.
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
        
        return $this->useMemcached ? $this->connection->set($unique_key, $data, $ttl) : $this->connection->set($unique_key, $data, 0, $ttl);
    }

    /**
     * Fetches cached data.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the \Memcache|\Memcached item.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     */
    public function read($key, $group = 'default')
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        $unique_key = $this->unique_key($key, $group);
        
        return $this->connection->get($unique_key);
    }

    /**
     * Updates the \Memcache|\Memcached based on unique key.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the \Memcache|\Memcached.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the \Memcache|\Memcached item. Default: 0 = will persist until cleared.
     */
    public function update($key, $data, $group = 'default', $ttl = 0)
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        $unique_key = $this->unique_key($key, $group);
        
        if ($this->_exists($unique_key, $group)) {
            return false;
        }
        
        return $this->useMemcached ? $this->connection->replace($unique_key, $data, $ttl) : $this->connection->replace($unique_key, $data, 0, $ttl);
    }

    /**
     * Deletes \Memcache|\Memcached based on unique key.
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of \Memcache|\Memcached.
     * @param string $group
     *            Optional. Where the cache contents are grouped. Default: 'default'.
     */
    public function delete($key, $group = 'default')
    {
        if (empty($group)) {
            $group = 'default';
        }
        
        $unique_key = $this->unique_key($key, $group);
        
        return $this->connection->delete($unique_key);
    }

    /**
     * Flushes \Memcache|\Memcached completely.
     *
     * @since 6.2.0
     */
    public function flush()
    {
        return $this->connection->flush();
    }

    /**
     * Add \Memcache|\Memcached servers.
     *
     * @since 6.2.0
     * @param array $servers
     *            An array of \Memcache|\Memcached servers.
     */
    public function addServer($servers)
    {
        $existingServers = [];
        
        foreach ($this->connection->getServerList() as $s) {
            $existingServers[$s['host'] . ':' . $s['port']] = true;
        }
        
        foreach ($servers as $server) {
            if (empty($existingServers) || ! isset($existingServers[$server->host . ':' . $server->port])) {
                $this->connection->addServer($server->$host, $server->$port, $server->$weight);
            }
        }
    }

    public function getStats()
    {
        return $this->useMemcached ? $this->connection->getStats() : $this->connection->getExtendedStats();
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

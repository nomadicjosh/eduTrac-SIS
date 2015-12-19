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
            $this->connection = new \Memcache();
        } else {
            $this->connection = new \Memcached('etsis');
        }
    }

    /**
     * Creates the \Memcache|\Memcached item.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::create()
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the \Memcache|\Memcached item.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the \Memcache|\Memcached item. Default: 0 = will persist until cleared.
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
        
        return $this->useMemcached ? $this->connection->set($unique_key, $data, $ttl) : $this->connection->set($unique_key, $data, 0, $ttl);
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
     *            Unique key of the \Memcache|\Memcached item.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     */
    public function read($key, $namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        $unique_key = $this->uniqueKey($key, $namespace);
        
        return $this->connection->get($unique_key);
    }

    /**
     * Updates the \Memcache|\Memcached based on unique key.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::update()
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of the \Memcache|\Memcached.
     * @param mixed $data
     *            Data that should be cached.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     * @param int $ttl
     *            Time to live sets the life of the \Memcache|\Memcached item. Default: 0 = will persist until cleared.
     */
    public function update($key, $data, $namespace = 'default', $ttl = 0)
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        $unique_key = $this->uniqueKey($key, $namespace);
        
        if ($this->_exists($unique_key, $namespace)) {
            return false;
        }
        
        return $this->useMemcached ? $this->connection->replace($unique_key, $data, $ttl) : $this->connection->replace($unique_key, $data, 0, $ttl);
    }

    /**
     * Deletes \Memcache|\Memcached based on unique key.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::delete()
     *
     * @since 6.2.0
     * @param int|string $key
     *            Unique key of \Memcache|\Memcached.
     * @param string $namespace
     *            Optional. Where the cache contents are namespaced. Default: 'default'.
     */
    public function delete($key, $namespace = 'default')
    {
        if (empty($namespace)) {
            $namespace = 'default';
        }
        
        $unique_key = $this->uniqueKey($key, $namespace);
        
        return $this->connection->delete($unique_key);
    }

    /**
     * Flushes \Memcache|\Memcached completely.
     *
     * {@inheritDoc}
     *
     * @see \app\src\Cache\etsis_Abstract_Cache::flush()
     *
     * @since 6.2.0
     */
    public function flush()
    {
        return $this->connection->flush();
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
        
        return $this->connection->increment($namespace, 10);
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
        if ($this->useMemcached == true) {
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
        
        if ($this->useMemcached == false) {
            foreach ($servers as $server) {
                $this->connection->addServer($server['host'], $server['port'], true, $server['weight']);
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

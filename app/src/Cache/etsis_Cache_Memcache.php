<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS \Memcache|\Memcached Class.
 *
 * @license GPLv3
 *         
 * @since 6.2.00
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

    public function __construct($useMemcached)
    {
        $this->useMemcached = $useMemcached;
        
        $ext = $this->useMemcached ? 'memcached' : 'memcache';
        
        if (! extension_loaded($ext)) {
            return new \app\src\Exception\Exception(sprintf(_t('Memcache requires PHP <strong>%s</strong> extension to be loaded.'), $ext), 'php_memcache_extension');
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
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the \Memcache|\Memcached item.
     * @param mixed $data
     *            Data that should be cached.
     * @param int $ttl
     *            Time to live sets the life of the \Memcache|\Memcached item.
     */
    public function create($key, $data, $ttl)
    {
        return $this->useMemcached ? $this->connection->set($key, $data, $ttl) : $this->connection->set($key, $data, 0, $ttl);
    }

    /**
     * Fetches cached data.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the \Memcache|\Memcached item.
     */
    public function read($key)
    {
        return $this->connection->get($key);
    }

    /**
     * Updates the \Memcache|\Memcached based on unique key.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the \Memcache|\Memcached.
     * @param mixed $data
     *            Data that should be cached.
     * @param int $ttl
     *            Time to live sets the life of the \Memcache|\Memcached item.
     */
    public function update($key, $data, $ttl)
    {
        return $this->useMemcached ? $this->connection->replace($key, $data, $ttl) : $this->connection->replace($key, $data, 0, $ttl);
    }

    /**
     * Deletes \Memcache|\Memcached based on unique key.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of \Memcache|\Memcached.
     */
    public function delete($key)
    {
        return $this->connection->delete($key);
    }

    /**
     * Flushes \Memcache|\Memcached completely.
     *
     * @since 6.2.00
     */
    public function flush()
    {
        return $this->connection->flush();
    }

    /**
     * Add \Memcache|\Memcached servers.
     *
     * @since 6.2.00
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
}

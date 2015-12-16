<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * eduTrac SIS XCache Cache Class.
 *
 * @license GPLv3
 *         
 * @since 6.2.00
 * @package eduTrac SIS
 * @subpackage Cache
 * @author Joshua Parker <joshmac3@icloud.com>
 */

class etsis_Cache_XCache extends \app\src\Cache\etsis_Abstract_cache
{
    public function __construct()
    {
        if (! extension_loaded('xcache')) {
            return new \app\src\Exception\Exception(_t('XCache requires PHP XCache extension to be installed and loaded.'), 'php_cache_extension');
        }
        return true;
    }
    
    /**
     * Creates the cache file.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param int $ttl
     *            Time to live sets the life of the cache file.
     */
    public function create($key, $data, $ttl)
    {
        return xcache_set($key, $data, $ttl);
    }

    /**
     * Fetches cached data.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the cache file.
     */
    public function read($key)
    {
        return xcache_isset($key) ? xcache_get($key) : false;
    }

    /**
     * Updates a cache file based on unique ID.
     * This method only exists for
     * CRUD completeness purposes and just basically calls the create method.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the cache file.
     * @param mixed $data
     *            Data that should be cached.
     * @param int $ttl
     *            Time to live sets the life of the cache file.
     */
    public function update($key, $data, $ttl)
    {
        return $this->create($key, $data, $ttl);
    }

    /**
     * Deletes a cache file based on unique key.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of cache file.
     */
    public function delete($key)
    {
        return xcache_unset($key);
    }

    /**
     * Flushes the cache completely.
     *
     * @since 6.2.00
     */
    public function flush()
    {
        for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++) {
            if (xcache_clear_cache(XC_TYPE_VAR, $i) === false) {
                return false;
            }
        }
        return true;
    }
}

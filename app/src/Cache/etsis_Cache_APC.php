<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS APC Cache Class.
 *
 * @license GPLv3
 *         
 * @since 6.2.00
 * @package eduTrac SIS
 * @subpackage Cache
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Cache_APC extends \app\src\Cache\etsis_Abstract_cache
{

    public function __construct()
    {
        if (! extension_loaded('apc') && ! ini_get('apc.enabled')) {
            return new \app\src\Exception\Exception(_t('APC requires PHP APC extension to be installed and loaded.'), 'php_cache_extension');
        }
        return true;
    }

    /**
     * Creates the APC cache item.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the APC cached item.
     * @param mixed $data
     *            Data that should be cached.
     * @param int $ttl
     *            Time to live sets the life of the APC cached item.
     */
    public function create($key, $data, $ttl)
    {
        return apc_store($key, $data, $ttl);
    }

    /**
     * Fetches cached data.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the APC cached item.
     */
    public function read($key)
    {
        return apc_fetch($key);
    }

    /**
     * Updates the APC cache based on unique key.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of the APC cache.
     * @param mixed $data
     *            Data that should be cached.
     * @param int $ttl
     *            Time to live sets the life of the APC cached item.
     */
    public function update($key, $data, $ttl)
    {
        return apc_store($key, $data, $ttl);
    }

    /**
     * Deletes cache based on unique key.
     *
     * @since 6.2.00
     * @param int|string $key
     *            Unique key of APC cache.
     */
    public function delete($key)
    {
        return apc_delete($key);
    }

    /**
     * Flushes the APC cache completely.
     *
     * @since 6.2.00
     */
    public function flush()
    {
        apc_clear_cache();
        apc_clear_cache('user');
    }
}

<?php
if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Cache API.
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

/**
 * Sets up object cache global and assigns it based on
 * the type of caching system used.
 *
 * @since 6.2.0
 */
function _etsis_cache_init()
{
    $cache_type = apply_filter('etsis_cache_type', 'file');
    
    $cache = new \app\src\Cache\Cache($cache_type);
    
    if(is_et_exception($cache)) {
        return $cache->getMessage();
    }
    return $cache;
}

/**
 * Adds data to the cache, if the cache key doesn't already exist.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @param int|string $key
 *            The cache key to use for retrieval later.
 * @param mixed $data
 *            The data to add to the cache.
 * @param int $expire
 *            Optional. When the cache data should expire, in seconds.
 *            Default: 120 seconds =  2 minutes.
 * @return bool False if cache key already exists, true on success.
 */
function etsis_cache_add($key, $data, $group = '', $expire = 120)
{
    /**
     * Filter the expire time for cache item.
     *
     * @since 6.2.0
     * @param int $expire
     *            When the cass data should expire, in seconds.
     */
    $ttl = apply_filter('etsis_cache_add_ttl', $expire);
    $cache = _etsis_cache_init();
    return $cache->create($key, $data, $group, (int) $ttl);
}

/**
 * Retrieves the cache contents from the cache by key and group.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @param int|string $key
 *            The key under which the cache contents are stored.
 * @return bool|mixed False on failure to retrieve contents or the cache
 *         contents on success.
 */
function etsis_cache_get($key, $group = '')
{
    $cache = _etsis_cache_init();
    return $cache->read($key, $group);
}

/**
 * Replaces the contents of the cache with new data.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @param int|string $key
 *            The key for the cache data that should be replaced.
 * @param mixed $data
 *            The new data to store in the cache.
 * @param int $expire
 *            Optional. When to expire the cache contents, in seconds.
 *            Default: 120 seconds = 2 minutes.
 * @return bool False if original value does not exist, true if contents were replaced
 */
function etsis_cache_replace($key, $data, $group = '', $expire = 120)
{
    /**
     * Filter the expire time for cache item.
     *
     * @since 6.2.0
     * @param int $expire
     *            When the cass data should expire, in seconds.
     */
    $ttl = apply_filter('etsis_cache_replace_ttl', $expire);
    $cache = _etsis_cache_init();
    return $cache->update($key, $data, $group, $ttl);
}

/**
 * Removes the cache contents matching key and group.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @param int|string $key
 *            What the contents in the cache are called.
 * @return bool True on successful removal, false on failure.
 */
function etsis_cache_delete($key, $group = '')
{
    $cache = _etsis_cache_init();
    return $cache->delete($key, $group);
}

/**
 * Removes all cache items.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @return bool False on failure, true on success
 */
function etsis_cache_flush()
{
    $cache = _etsis_cache_init();
    return $cache->flush();
}
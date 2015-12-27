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
 * Adds data to the cache, if the cache key doesn't already exist.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @param int|string $key
 *            The cache key to use for retrieval later.
 * @param mixed $data
 *            The data to add to the cache.
 * @param string $namespace
 *            Optional. Where the cache contents are namespaced.
 * @param int $expire
 *            Optional. When the cache data should expire, in seconds.
 *            Default: 120 seconds = 2 minutes.
 * @return bool False if cache key already exists, true on success.
 */
function etsis_cache_add($key, $data, $namespace = '', $expire = 120)
{
    /**
     * Filter the expire time for cache item.
     *
     * @since 6.2.0
     * @param int $expire
     *            When the cache data should expire, in seconds.
     */
    $ttl = apply_filter('etsis_cache_increase_ttl', $expire);
    $cache = _etsis_cache_init();
    return $cache->create($key, $data, $namespace, (int) $ttl);
}

/**
 * Retrieves the cache contents from the cache by key and group.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @param int|string $key
 *            The key under which the cache contents are stored.
 * @param string $namespace
 *            Optional. Where the cache contents are namespaced.
 * @return bool|mixed False on failure to retrieve contents or the cache
 *         contents on success.
 */
function etsis_cache_get($key, $namespace = '')
{
    $cache = _etsis_cache_init();
    return $cache->read($key, $namespace);
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
 * @param string $namespace
 *            Optional. Where the cache contents are namespaced.
 * @param int $expire
 *            Optional. When to expire the cache contents, in seconds.
 *            Default: 120 seconds = 2 minutes.
 * @return bool False if original value does not exist, true if contents were replaced
 */
function etsis_cache_replace($key, $data, $namespace = '', $expire = 120)
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
    return $cache->update($key, $data, $namespace, (int) $ttl);
}

/**
 * Removes the cache contents matching key and group.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @param int|string $key
 *            What the contents in the cache are called.
 * @param string $namespace
 *            Optional. Where the cache contents are namespaced.
 * @return bool True on successful removal, false on failure.
 */
function etsis_cache_delete($key, $namespace = '')
{
    $cache = _etsis_cache_init();
    return $cache->delete($key, $namespace);
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

/**
 * Removes all cache items from a particular namespace.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @param string $value
 *            The namespace to delete from.
 * @return bool False on failure, true on success
 */
function etsis_cache_flush_namespace($value)
{
    $cache = _etsis_cache_init();
    return $cache->flushNamespace($value);
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
 *            Optional. Where the cache contents are namespaced. Default: 'default'.
 * @param int $expire
 *            Optional. When to expire the cache contents, in seconds.
 *            Default: 120 seconds = 2 minutes.
 * @return bool Returns true if the cache was set and false otherwise.
 */
function etsis_cache_set($key, $data, $namespace = '', $expire = 120)
{
    /**
     * Filter the expire time for cache item.
     *
     * @since 6.2.0
     * @param int $expire
     *            When the cass data should expire, in seconds.
     */
    $ttl = apply_filter('etsis_cache_increase_ttl', $expire);
    $cache = _etsis_cache_init();
    return $cache->set($key, $data, $namespace, (int) $ttl);
}

/**
 * Returns the stats of the cache.
 *
 * Gives the cache hits, cache misses and cache uptime.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 */
function etsis_cache_get_stats()
{
    $cache = _etsis_cache_init();
    return $cache->getStats();
}

/**
 * Increments numeric cache item's value.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @param int|string $key
 *            The cache key to increment
 * @param int $offset
 *            Optional. The amount by which to increment the item's value. Default: 1.
 * @param string $namespace
 *            Optional. The namespace the key is in.
 * @return false|int False on failure, the item's new value on success.
 */
function etsis_cache_increment($key, $offset = 1, $namespace = '')
{
    $cache = _etsis_cache_init();
    return $cache->inc($key, (int) $offset, $namespace);
}

/**
 * Decrements numeric cache item's value.
 *
 * @since 6.2.0
 * @uses _etsis_cache_init()
 * @param int|string $key
 *            The cache key to decrement.
 * @param int $offset
 *            Optional. The amount by which to decrement the item's value. Default: 1.
 * @param string $namespace
 *            Optional. The namespace the key is in.
 * @return false|int False on failure, the item's new value on success.
 */
function etsis_cache_decrement($key, $offset = 1, $namespace = '')
{
    $cache = _etsis_cache_init();
    return $cache->dec($key, (int) $offset, $namespace);
}
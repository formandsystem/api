<?php

namespace Formandsystem\Api\Cache;

interface CacheInterface
{
    /**
     * Determine if an item exists in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Retrieve an item from the cache by key.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Store an item in the cache.
     *
     * @param string        $key
     * @param mixed         $value
     * @param \DateTime|int $minutes
     */
    public function put($key, $value, $minutes);

    /**
     * Store an item in the cache indefinitely.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function forever($key, $value);

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function forget($key);
}

<?php

namespace Formandsystem\Api\Cache;

class LaravelCache implements CacheInterface
{
    protected $cache;

    public function __construct()
    {
        $this->cache = app('cache');
    }

    public function has($key)
    {
        return $this->cache->has($key);
    }

    public function get($key)
    {
        return $this->cache->get($key);
    }

    public function put($key, $value, $minutes = null)
    {
        return $this->cache->put($key, $value, $minutes);
    }

    public function forever($key, $value)
    {
        return $this->cache->forever($key, $value);
    }

    public function forget($key)
    {
        return $this->cache->forget($key);
    }
}

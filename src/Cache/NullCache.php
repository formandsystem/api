<?php

namespace Formandsystem\Api\Cache;

use Formandsystem\Api\Interfaces\Cache as CacheInterface;

class NullCache implements CacheInterface
{

    public function has($key)
    {
        return false;
    }

    public function get($key)
    {
    }

    public function put($key, $value, $minutes = null)
    {
    }

    public function forever($key, $value)
    {
    }

    public function forget($key)
    {
    }
}

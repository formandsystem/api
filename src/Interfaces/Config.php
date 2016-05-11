<?php

namespace Formandsystem\Api\Interfaces;

interface Config{
    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null);
}

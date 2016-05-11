<?php

namespace Formandsystem\Api;

use Formandsystem\Api\Interfaces\Config;
use Formandsystem\Api\Interfaces\Cache;

class Api{

    public function __construct(Config $config, Cache $cache)
    {
        $this->config = $config;
        $this->cache = $cache;
        // setup client
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->config->get('api.url'),
            'exceptions' => false,
        ]);
    }

    public function get(){

    }
    public function post(){

    }
    public function patch(){

    }
    public function put(){

    }
    public function delete(){

    }
}

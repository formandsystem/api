<?php

use Formandsystem\Api\Api;
use Formandsystem\Api\Config;
use Formandsystem\Api\TestBase;

class CacheTest extends TestBase
{
    public function setUp()
    {
        $this->client = Mockery::mock('GuzzleHttp\Client');
        // setup config
        $this->config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => false,
            'scopes'        => ['content.get'],
        ]);
    }

    public function testAccessTokenCache()
    {
        $cache = Mockery::mock('Formandsystem\Api\Cache\CacheInterface')
            ->shouldReceive('has')
            ->with('access_token'.$this->config->client_id)
            ->times(1)
            ->andReturn(true)->getMock();

        $cache->shouldReceive('get')
            ->with('access_token'.$this->config->client_id)
            ->times(2)
            ->andReturn([
                'scopes' => $this->config->scopes,
                'token'  => $this->token,
            ]);
        $this->client->shouldNotReceive('post')->with($this->config->url.'/tokens');
        $this->client->shouldReceive('get')->andReturn($this->response('success'));
        // setup default api
        $api = new Api($this->config->toArray(), $cache, $this->client);

        $result = $api->get('/testToGetToken');
        $this->assertSame($result, $this->data['success']);
    }
}

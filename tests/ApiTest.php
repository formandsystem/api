<?php
use PHPUnit\Framework\TestCase;
use Formandsystem\Api\Api;
use Formandsystem\Api\Config;
use Formandsystem\Api\Cache\NullCache;
use Psr\Http\Message\ResponseInterface;

class ApiTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testInitApi()
    {
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => false,
            'scopes'        => ['content.get'],
        ]);

        $api = new Api($config, new NullCache(), new GuzzleHttp\Client([
            'exceptions' => false
        ]));

        $this->assertInstanceOf(Api::class, $api);
    }

    public function testGet()
    {
        $token = '123456789';
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => false,
            'scopes'        => ['content.get'],
        ]);

        $client = Mockery::mock('GuzzleHttp\Client');
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');

        $client->shouldReceive('post')->times(1)->with($config->url.'/tokens',[
            'headers' => [
                'Accept' => 'application/json',
            ],
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $config->client_id,
                'client_secret' => $config->client_secret,
                'scope'         => implode(',', array_map('trim', $config->scopes)),
            ],
        ])->andReturn($response);

        $response->shouldReceive('getBody')->andReturn(json_encode([
            'data' => [
                'id' => $token,
                'attributes' => [
                    'expires_in' => time() + 3600 // now + 1h
                ]
            ]
        ]));

        $client->shouldReceive('get')->times(1)->with('http://api.formandsystem.com/test', [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ]
        ])->andReturn($response);

        $api = new Api($config, new NullCache(), $client);

        $api->get('/test');

        $this->assertInstanceOf(Api::class, $api);
    }
}

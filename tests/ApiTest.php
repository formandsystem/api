<?php

use Formandsystem\Api\Api;
use Formandsystem\Api\Cache\NullCache;
use Formandsystem\Api\Config;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    protected $token = '123456789';
    protected $headers = [
        'Accept'        => 'application/json',
        'Authorization' => 'Bearer 123456789',
    ];
    protected $config;
    protected $client;
    protected $response;
    protected $api;
    protected $data = [
        'success' => [
            'data' => [
                'id' => 1234,
            ],
        ],
        'error'  => [
            'error' => [
                'message'     => 'Check your client id and client secret or you access token.',
                'status_code' => 403,
            ],
        ],
    ];

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
        // setup default api
        $this->api = new Api($this->config->toArray(), new NullCache(), $this->client);
    }

    public function response($type = 'success')
    {
        return Mockery::mock('Psr\Http\Message\ResponseInterface')
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn(json_encode($this->data[$type]))->getMock();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function apiToken()
    {
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');

        $this->client->shouldReceive('post')->times(1)->with($this->config->url.'/tokens', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->config->client_id,
                'client_secret' => $this->config->client_secret,
                'scope'         => implode(',', array_map('trim', $this->config->scopes)),
            ],
        ])->andReturn($response);

        $response->shouldReceive('getBody')->andReturn(json_encode([
            'data' => [
                'id'         => $this->token,
                'attributes' => [
                    'expires_in' => time() + 3600, // now + 1h
                ],
            ],
        ]));
    }

    public function testInitApi()
    {
        $this->assertInstanceOf(Api::class, $this->api);
    }

    public function testAccessToken()
    {
        // Mock API Token stuff
        $this->apiToken();
        // real test
        $this->client->shouldReceive('get')->times(1)->with($this->config->url.'/testToGetToken', [
            'headers' => $this->headers,
        ])->andReturn($this->response('success'));

        $result = $this->api->get('/testToGetToken');
        $this->assertSame($result, $this->data['success']);
    }

    public function testAccessTokenCacheDifferentScope()
    {
        // Mock API Token stuff
        $this->apiToken();
        // setup Cache mock
        $cache = Mockery::mock('Formandsystem\Api\Interfaces\Cache')->shouldIgnoreMissing()
                ->shouldReceive('has')->times(1)->andReturn(true)
                ->shouldReceive('get')->times(1)->with('access_token'.$this->config->client_id)->andReturn([
                    'scopes' => ['client.delete'],
                ])->getMock();
        // real test
        $this->client->shouldReceive('get')->times(1)->with($this->config->url.'/testToGetToken', [
            'headers' => $this->headers,
        ])->andReturn($this->response('success'));

        $api = new Api($this->config->toArray(), $cache, $this->client);
        $result = $api->get('/testToGetToken');
        $this->assertSame($result, $this->data['success']);
    }

    public function testGet()
    {
        // Mock API Token stuff
        $this->apiToken();
        // Mock get request
        $this->client->shouldReceive('get')->times(1)->with($this->config->url.'/collections', [
            'headers' => $this->headers,
        ])->andReturn($this->response('success'));

        $result = $this->api->get('/collections');
        $this->assertEquals($this->data['success'], $result);
    }

    public function testGetError()
    {
        $this->expectException(ErrorException::class);
        // Mock API Token stuff
        $this->apiToken();
        // real test
        $this->client->shouldReceive('get')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => $this->headers,
        ])->andReturn($this->response('error'));

        $result = $this->api->get('/collections');

        $this->assertEquals($this->data['error'], $result);
    }

    public function testPost()
    {
        // Mock API Token stuff
        $this->apiToken();

        $this->client->shouldReceive('post')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => $this->headers,
            'body'    => json_encode([
                'data' => $this->data['success'],
            ]),
        ])->andReturn($this->response('success'));

        $result = $this->api->post('/collections', $this->data['success']);
        $this->assertEquals($this->data['success'], $result);
    }

    public function testPostError()
    {
        $this->expectException(ErrorException::class);
        // Mock API Token stuff
        $this->apiToken();

        $this->client->shouldReceive('post')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => $this->headers,
            'body'    => json_encode([
                'data' => [],
            ]),
        ])->andReturn($this->response('error'));

        $result = $this->api->post('/collections', []);
        $this->assertEquals($this->data['error'], $result);
    }

    public function testPatch()
    {
        // Mock API Token stuff
        $this->apiToken();

        $this->client->shouldReceive('patch')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => $this->headers,
            'body'    => json_encode([
                'data' => $this->data['success'],
            ]),
        ])->andReturn($this->response('success'));

        $result = $this->api->patch('/collections', $this->data['success']);
        $this->assertEquals($this->data['success'], $result);
    }

    public function testPatchError()
    {
        $this->expectException(ErrorException::class);
        // Mock API Token stuff
        $this->apiToken();

        $this->client->shouldReceive('patch')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => $this->headers,
            'body'    => json_encode([
                'data' => [],
            ]),
        ])->andReturn($this->response('error'));

        $result = $this->api->patch('/collections', []);
        $this->assertEquals($this->data['error'], $result);
    }

    public function testPut()
    {
        // Mock API Token stuff
        $this->apiToken();

        $this->client->shouldReceive('put')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => $this->headers,
            'body'    => json_encode([
                'data' => $this->data['success'],
            ]),
        ])->andReturn($this->response('success'));

        $result = $this->api->put('/collections', $this->data['success']);
        $this->assertEquals($this->data['success'], $result);
    }

    public function testPutError()
    {
        $this->expectException(ErrorException::class);
        // Mock API Token stuff
        $this->apiToken();

        $this->client->shouldReceive('put')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => $this->headers,
            'body'    => json_encode([
                'data' => [],
            ]),
        ])->andReturn($this->response('error'));

        $result = $this->api->put('/collections', []);
        $this->assertEquals($this->data['error'], $result);
    }

    public function testDelete()
    {
        // Mock API Token stuff
        $this->apiToken();

        $this->client->shouldReceive('delete')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => $this->headers,
        ])->andReturn($this->response('success'));

        $result = $this->api->delete('/collections');
        $this->assertEquals($this->data['success'], $result);
    }

    public function testDeleteWithBody()
    {
        // Mock API Token stuff
        $this->apiToken();

        $this->client->shouldReceive('delete')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => $this->headers,
            'body'    => json_encode([
                'data' => 'test',
            ]),
        ])->andReturn($this->response('success'));

        $result = $this->api->delete('/collections', 'test');
        $this->assertEquals($this->data['success'], $result);
    }

    public function testDeleteError()
    {
        $this->expectException(ErrorException::class);
        // Mock API Token stuff
        $this->apiToken();

        $this->client->shouldReceive('delete')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => $this->headers,
        ])->andReturn($this->response('error'));

        $result = $this->api->delete('/collections');
        $this->assertEquals($this->data['error'], $result);
    }
}

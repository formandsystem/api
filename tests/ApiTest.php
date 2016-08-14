<?php

use Formandsystem\Api\Api;
use Formandsystem\Api\Cache\NullCache;
use Formandsystem\Api\Config;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    protected $token;
    protected $config;
    protected $client;
    protected $response;

    public function setUp()
    {
        $this->token = '123456789';
        $this->client = Mockery::mock('GuzzleHttp\Client');

        $this->config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => false,
            'scopes'        => ['content.get'],
        ]);
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
        $api = new Api($this->config->toArray(), new NullCache(), new GuzzleHttp\Client([
            'exceptions' => false,
        ]));

        $this->assertInstanceOf(Api::class, $api);
    }

    public function testAccessToken()
    {
        // Mock API Token stuff
        $this->apiToken();
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->times(1)->andReturn(json_encode([
            'data' => [],
        ]));
        // real test
        $this->client->shouldReceive('get')->times(1)->with('http://api.formandsystem.com/testToGetToken', [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$this->token,
            ],
        ])->andReturn($response);

        $api = new Api($this->config->toArray(), new NullCache(), $this->client);
        $api->get('/testToGetToken');
    }

    public function testGet()
    {
        // Mock API Token stuff
        $this->apiToken();
        $responseData['data'] = [
            'id'         => '12345',
            'type'       => 'collections',
            'attributes' => [
                'name' => 'name',
                'slug' => 'slug',
            ],
        ];
        // real test
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->times(1)->andReturn(json_encode(
            $responseData
        ));

        $this->client->shouldReceive('get')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$this->token,
            ],
        ])->andReturn($response);

        $api = new Api($this->config->toArray(), new NullCache(), $this->client);
        $result = $api->get('/collections');

        $this->assertEquals($responseData, $result);
    }

    public function testGetError()
    {
        $this->expectException(ErrorException::class);
        // Mock API Token stuff
        $this->apiToken();
        $responseData['error'] = [
            'message'     => 'Check your client id and client secret or you access token.',
            'status_code' => 403,
        ];
        // real test
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->times(1)->andReturn(json_encode(
            $responseData
        ));

        $this->client->shouldReceive('get')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$this->token,
            ],
        ])->andReturn($response);

        $api = new Api($this->config->toArray(), new NullCache(), $this->client);
        $result = $api->get('/collections');

        $this->assertEquals($responseData, $result);
    }

    public function testPost()
    {
        // Mock API Token stuff
        $this->apiToken();
        $data = [
            'type'       => 'collections',
            'attributes' => [
                'name' => 'name',
                'slug' => 'slug',
                'type' => 'pages',
            ],
        ];
        
        $responseData['data'] = [
            'type'       => 'collections',
            'id'         => '110790d5-5179-4332-9c85-c8b03d8d1750',
            'attributes' => [
                'name'       => 'name',
                'slug'       => 'slug',
                'type'       => 'pages',
                'position'   => null,
                'is_trashed' => false,
            ],
            'links' =>
            [
                'self' => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750',
            ],
            'relationships' =>
            [
                'pages' =>
                    [
                        'links' =>
                            [
                                'self'    => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/relationships/pages',
                                'related' => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/pages',
                            ],
                        'data' =>
                            [
                            ],
                    ],
                'ownedByPages' =>
                    [
                        'links' =>
                            [
                                'self'    => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/relationships/ownedByPages',
                                'related' => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/ownedByPages',
                            ],

                    ],
                'collections' =>
                    [
                        'links' =>
                            [
                                'self'    => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/relationships/collections',
                                'related' => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/collections',
                            ],
                    ],
                'ownedByCollections' =>
                    [
                        'links' =>
                            [
                                'self'    => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/relationships/ownedByCollections',
                                'related' => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/ownedByCollections',
                            ],
                    ],
                'fragments' =>
                    [
                        'links' =>
                            [
                                'self'    => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/relationships/fragments',
                                'related' => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/fragments',
                            ],
                        'data' =>
                            [
                            ],
                    ],
                'ownedByFragments' =>
                    [
                        'links' =>
                            [
                                'self'    => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/relationships/ownedByFragments',
                                'related' => 'http://formandsystem-api.dev/collections/110790d5-5179-4332-9c85-c8b03d8d1750/ownedByFragments',
                            ],
                    ],
            ],
        ];

        // real test
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->times(1)->andReturn(json_encode(
            $responseData
        ));

        $this->client->shouldReceive('post')->times(1)->with('http://api.formandsystem.com/collections', [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$this->token,
            ],
            'body' => json_encode([
                'data' => $data,
            ]),
        ])->andReturn($response);

        $api = new Api($this->config->toArray(), new NullCache(), $this->client);
        $result = $api->post('/collections', $data);

        $this->assertEquals($responseData, $result);
    }
}

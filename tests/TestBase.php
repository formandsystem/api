<?php

namespace Formandsystem\Api;

use PHPUnit\Framework\TestCase;

class TestBase extends TestCase
{
    protected $config;
    protected $client;
    protected $api;
    protected $token = '123456789';
    protected $headers = [
        'Accept'        => 'application/json',
        'Authorization' => 'Bearer 123456789',
    ];
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
        $this->client = \Mockery::mock('GuzzleHttp\Client');
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

    public function tearDown()
    {
        \Mockery::close();
    }

    protected function response($type = 'success')
    {
        return \Mockery::mock('Psr\Http\Message\ResponseInterface')
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn(json_encode($this->data[$type]))->getMock();
    }
}

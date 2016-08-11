<?php
use PHPUnit\Framework\TestCase;
use Formandsystem\Api\Api;
use Formandsystem\Api\Config;
use GuzzleHttp;

class ApiTest extends TestCase
{
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

        $api = new Api($config, NULL, new GuzzleHttp\Client([
            'exceptions' => false
        ]));

        $this->assertInstanceOf(Api::class, $api);
    }

    public function testGet()
    {
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => false,
            'scopes'        => ['content.get'],
        ]);

        $api = new Api($config, NULL, new GuzzleHttp\Client([
            'exceptions' => false
        ]));
        print_r($api->get('/test'));
        $this->assertInstanceOf(Api::class, $api);
    }
}

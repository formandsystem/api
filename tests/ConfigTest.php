<?php

use Formandsystem\Api\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testMissingArguments()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([]);
    }

    public function testInvalidUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 1234,
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => true,
            'scopes'        => ['content.get'],
        ]);
    }

    public function testNullUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => null,
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => true,
            'scopes'        => ['content.get'],
        ]);
    }

    public function testInvalidVersion()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1.2,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => true,
            'scopes'        => ['content.get'],
        ]);
    }

    public function testNullVersion()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => null,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => true,
            'scopes'        => ['content.get'],
        ]);
    }

    public function testInvalidClientId()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => 1234,
            'client_secret' => '1234-12344-1231231',
            'cache'         => true,
            'scopes'        => ['content.get'],
        ]);
    }

    public function testNullClientId()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => null,
            'client_secret' => '1234-12344-1231231',
            'cache'         => true,
            'scopes'        => ['content.get'],
        ]);
    }

    public function testInvalidClientSecret()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => true,
            'cache'         => true,
            'scopes'        => ['content.get'],
        ]);
    }

    public function testNullClientSecret()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => null,
            'cache'         => true,
            'scopes'        => ['content.get'],
        ]);
    }

    public function testInvalidCache()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => 'Cache',
            'scopes'        => ['content.get'],
        ]);
    }

    public function testNullCache()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => null,
            'scopes'        => ['content.get'],
        ]);
    }

    public function testInvalidScopes()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => true,
            'scopes'        => 'content.get',
        ]);
    }

    public function testNullScopes()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => false,
            'scopes'        => null,
        ]);
    }

    public function testCreateConfig()
    {
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => false,
            'scopes'        => ['content.get'],
        ]);
        $this->assertInstanceOf(Config::class, $config);
    }

    public function testConfigGetProperties()
    {
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => false,
            'scopes'        => ['content.get'],
        ]);
        $this->assertEquals('http://api.formandsystem.com', $config->url);
    }

    public function testConfigGetPropertiesArray()
    {
        $config = new Config([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => false,
            'scopes'        => ['content.get'],
        ]);
        $this->assertEquals([
            'url'           => 'http://api.formandsystem.com',
            'version'       => 1,
            'client_id'     => '1234-12344-1231231',
            'client_secret' => '1234-12344-1231231',
            'cache'         => false,
            'scopes'        => ['content.get'],
        ], $config->toArray());
    }
}

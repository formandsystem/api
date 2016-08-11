<?php
use PHPUnit\Framework\TestCase;
use Formandsystem\Api\Config;

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
            'url'           => NULL,
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
            'version'       => NULL,
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
            'client_id'     => NULL,
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
            'client_secret' => NULL,
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
            'cache'         => NULL,
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
            'scopes'        => NULL,
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

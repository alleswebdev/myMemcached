<?php
require_once("../lib/Memcached.php");

use Alleswebdev\Tools\Memcached;
use PHPUnit\Framework\TestCase;

Class MemcachedTest extends TestCase
{
    /**
     * @dataProvider connectValidProvider
     */
    public function testConnect($host, $port)
    {
        $app = new Memcached();
        $this->assertTrue($app->connect($host, $port));
    }

    /**
     * @depends      testConnect
     * @dataProvider connectInValidProvider
     */
    public function testConnectInvalid($host, $port)
    {
        $app = new Memcached();
        $this->assertFalse($app->connect($host, $port));
    }

    public function testSetDataWithoutConnection()
    {
        $app = new Memcached();
        $this->assertFalse($app->set("test", "432"));
        $this->assertFalse($app->get("test"));
        $this->assertFalse($app->delete("test"));
    }

    public function testSetData()
    {
        $app = new Memcached();
        $app->connect();
        $this->assertTrue($app->set("test", "432"));
    }

    public function testDeleteData()
    {
        $app = new Memcached();
        $app->connect();
        $app->set("test", "432");
        $this->assertTrue($app->delete("test"));
        $this->assertFalse($app->delete("test2"));
    }

    public function testGetData()
    {
        $app = new Memcached();
        $app->connect();
        $app->set("test", "432");
        $this->assertEquals($app->get("test"), [
            'key' => 'test',
            'flag' => '0',
            'size' => '3',
            'value' => '432'
        ]);
        $this->assertFalse($app->get("test2"));
    }

    public function connectValidProvider()
    {
        return [
            ['localhost', 11211],
            ['127.0.0.1', 11211],
        ];
    }

    public function connectInValidProvider()
    {
        return [
            ['-1', 22],
            ['0.0.0.1', 11211],
        ];
    }
}

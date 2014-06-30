<?php

namespace Zoya\Tests;

use Zoya\ProxyServer;
/**
 * @covers \Zoya\Phantomjs
 */
class ProxyServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidation()
    {
        $proxy = new ProxyServer('httttp:/foo');
    }

    public function testParseUrl()
    {
        $proxy = new ProxyServer('http://username:password@hostname/path?arg=value#anchor');

        $this->assertEquals('http', $proxy->getScheme());
        $this->assertEquals('hostname', $proxy->getHost());
        $this->assertEquals('username', $proxy->getUser());
        $this->assertEquals('password', $proxy->getPassword());
        $this->assertEquals('/path', $proxy->getPath());
        $this->assertEquals('arg=value', $proxy->getQuery());
        $this->assertEquals('anchor', $proxy->getFragment());
        $this->assertEquals('http', $proxy->getType());
    }

    public function testType()
    {
        $proxy = new ProxyServer('http://username:password@hostname/path?arg=value#anchor');
        $this->assertEquals('http', $proxy->getType());

        $proxy->setType('socks5');
        $this->assertEquals('socks5', $proxy->getType());
    }

    /**
     * @expectedException \Assert\AssertionFailedException
     */
    public function testInvalidType()
    {
        $proxy = new ProxyServer('http://username:password@hostname/path?arg=value#anchor');
        $proxy->setType('foo');
    }

    public function testStatus()
    {
        $proxy = new ProxyServer('http://username:password@hostname/path?arg=value#anchor');
        $this->assertEquals(ProxyServer::STATUS_UNKNOWN, $proxy->getStatus());

        $proxy->setStatus(ProxyServer::STATUS_ALIVE);
        $this->assertEquals(ProxyServer::STATUS_ALIVE, $proxy->getStatus());
    }

    /**
     * @expectedException \Assert\AssertionFailedException
     */
    public function testInvalidStatus()
    {
        $proxy = new ProxyServer('http://username:password@hostname/path?arg=value#anchor');
        $proxy->setStatus('foo');
    }

}

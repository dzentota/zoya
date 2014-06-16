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
        $url = 'httrp://username:password@hostname/path?arg=value#anchor';
        $proxy = new ProxyServer('http://username:password@hostname/path?arg=value#anchor');

        $this->assertEquals('http', $proxy->getScheme());
        $this->assertEquals('hostname', $proxy->getHost());
        $this->assertEquals('username', $proxy->getUser());
        $this->assertEquals('password', $proxy->getPassword());
        $this->assertEquals('/path', $proxy->getPath());
        $this->assertEquals('arg=value', $proxy->getQuery());
        $this->assertEquals('anchor', $proxy->getFragment());

    }
}

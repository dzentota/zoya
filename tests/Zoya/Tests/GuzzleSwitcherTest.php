<?php

namespace Zoya\Tests;

use Guzzle\Service\Client;
use Zoya\ProxySwitcher;

class ValeraLoaderGuzzleAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testSwitchProxy()
    {
        $guzzle = $this->getMockBuilder('\Guzzle\Http\Client')
            ->getMock();
        $loader = $this->getMockBuilder('\Valera\Loader\Guzzle')
                  ->setConstructorArgs([$guzzle])
                  ->setMethods(['getClient'])
                  ->getMock();
        $loader->expects($this->any())->method('getClient')->will($this->returnValue($guzzle));
        $proxyUrl = 'http://8.8.8.8:8080';
        $proxyServer = new \Zoya\ProxyServer($proxyUrl);
        $proxiesList = [$proxyServer];

        $identity = $this->getMockBuilder('\Zoya\Loader\ChangeIdentity\Always')
                    ->getMock();
        $proxy = new ProxySwitcher($loader, $guzzle, $proxiesList, $identity);
        $adapter = new ProxySwitcher\ValeraLoaderGuzzleAdapter($proxy);
        $adapter->switchProxy();
        $config = $guzzle->getConfig()->toArray();
        $this->assertArrayHasKey(\Guzzle\Http\Client::REQUEST_OPTIONS, $config);
        $this->assertEquals($config[Client::REQUEST_OPTIONS]['proxy'], $proxyUrl);
    }
}

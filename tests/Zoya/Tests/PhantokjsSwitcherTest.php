<?php

namespace Zoya\Tests;

use Zoya\ProxySwitcher;

class ValeraLoaderGuzzleAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testSwitchProxy()
    {
        $loader = $this->getMockBuilder('\Zoya\Loader\Phantomjs')
//                    ->setMethods(['addCliOptions'])
                    ->getMock();
//        $loader->expects($this->once())->method('addCliOptions');
        $proxyUrl = 'http://8.8.8.8:8080';
        $proxyServer = new \Zoya\ProxyServer($proxyUrl);
        $proxiesList = [$proxyServer];

        $identity = $this->getMockBuilder('\Zoya\Loader\ChangeIdentity\Always')
            ->getMock();
        $proxy = new ProxySwitcher($loader, null, $proxiesList, $identity);
        $adapter = new ProxySwitcher\ZoyaLoaderPhantomjsAdapter($proxy);
        $adapter->switchProxy();
        $config = $loader->getCliOptions();
        print_r($config);
    }
}

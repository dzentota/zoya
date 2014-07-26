<?php
require __DIR__ . '/../vendor/autoload.php';

$guzzle = new \GuzzleHttp\Client(['defaults'=> ['timeout'=>1]]);
$loader = new \Valera\Loader\Guzzle($guzzle);

$proxyServer = new \Zoya\ProxyServer('http://110.169.100.27:8080');
$proxiesList = new \Zoya\InfiniteList([$proxyServer], new \Zoya\Coin\Always());

$proxySwitcher = new Zoya\ProxySwitcher\Generic($proxiesList);

$loaderWithProxy = new \Zoya\Loader\Proxy\Guzzle($loader, $proxySwitcher, $guzzle);

$resource = new \Valera\Resource('http://web-customize.com/', null, \Valera\Resource::METHOD_GET );

$checker = new Zoya\ProxyChecker($loaderWithProxy, $resource, function($content) {
    return (bool) strpos($content, 'Ninja-neer');
});

try {
    if ($checker->check()) {
        echo "proxy OK: " . $loaderWithProxy->getSwitcher()->getProxy()->getServer();
    } else {
        echo "something went wrong with proxy: " . $loaderWithProxy->getSwitcher()->getProxy()->getServer();
    }
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo $e->getMessage() . "\n";
    echo $e->getRequest() . "\n";
}


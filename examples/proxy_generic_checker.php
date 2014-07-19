<?php
require __DIR__ . '/../vendor/autoload.php';

$guzzle = new \GuzzleHttp\Client(['defaults'=> ['timeout'=>2]]);
$loader = new \Valera\Loader\Guzzle($guzzle);

$proxiesList = new Zoya\ProxyList(['http://200.84.3.227:8080']);

$coin = new \Zoya\Coin\Random();

$proxy = new \Zoya\Proxy($coin, $proxiesList);

$loaderWithProxy = new \Zoya\ProxySwitcher\GuzzleSwitcher($loader, $proxy, $guzzle);

$resource = new \Valera\Resource('http://web-customize.com/', null, \Valera\Resource::METHOD_GET );

$checker = new Zoya\ProxyChecker\Generic($loaderWithProxy, $resource, function($content) {
    return (bool) strpos($content, 'Ninja-neer');
});
try {
    if ($checker->check()) {
        echo "proxy OK: " . $loaderWithProxy->getProxyServer()->getServer();
    } else {
        echo "something went wrong with proxy: " . $loaderWithProxy->getProxyServer()->getServer();
    }
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo $e->getRequest() . "\n";
    if ($e->hasResponse()) {
        echo $e->getResponse() . "\n";
    }
}


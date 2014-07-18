<?php
require __DIR__ . '/../vendor/autoload.php';

$guzzle = new \GuzzleHttp\Client();
$loader = new \Valera\Loader\Guzzle($guzzle);

$proxiesList = new Zoya\ProxyList(['http://110.169.100.27:8080']);

$coin = new \Zoya\Coin\Random();

$proxy = new \Zoya\Proxy($coin, $proxiesList);

$loaderWithProxy = new \Zoya\ProxySwitcher\GuzzleSwitcher($loader, $proxy, $guzzle);

$resource = new \Valera\Resource('http://web-customize.com/', null, \Valera\Resource::METHOD_GET );

$checker = new Zoya\ProxyChecker\Generic($loaderWithProxy, $resource, function($content) {
    return (bool) strpos($content, 'Ninja-neer');
});

var_dump($checker->check());

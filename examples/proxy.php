<?php
require __DIR__ . '/../vendor/autoload.php';

$guzzle = new \GuzzleHttp\Client();
$loader = new \Valera\Loader\Guzzle($guzzle);

$proxyServer = new \Zoya\ProxyServer('http://110.169.100.27:8080');
$proxiesList = new \Zoya\InfiniteList([$proxyServer], new \Zoya\Coin\Always());

$proxySwitcher = new Zoya\ProxySwitcher\Generic($proxiesList);

$loaderWithProxy = new \Zoya\Loader\Proxy\Guzzle($loader, $proxySwitcher, $guzzle);

$resource = new \Valera\Resource('http://web-customize.com/', null, \Valera\Resource::METHOD_GET);
$result = new \Valera\Loader\Result();

$loaderWithProxy->load($resource, $result);

print_r($result);

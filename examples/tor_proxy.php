<?php

require __DIR__ . '/../vendor/autoload.php';


$guzzle = new \GuzzleHttp\Client();
$loader = new \Valera\Loader\Guzzle($guzzle);

$proxyServer = new \Zoya\TorProxyServer('socks5://127.0.0.1:9150');
$proxiesList = new \Zoya\InfiniteList([$proxyServer], new \Zoya\Coin\Always());

$proxySwitcher = new Zoya\ProxySwitcher\Tor($proxiesList, new \Zoya\Coin\Always());

$loaderWithProxy = new \Zoya\Loader\Proxy\Guzzle($loader, $proxySwitcher, $guzzle);

$resource = new \Valera\Resource('https://www.whatismyip.com/', null, \Valera\Resource::METHOD_GET );
$result = new \Valera\Loader\Result();

$loaderWithProxy->load($resource, $result);
file_put_contents('/tmp/myip', $result->getContent());

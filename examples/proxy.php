<?php

$guzzle = new \Guzzle\Http\Client();
$loader = new \Valera\Loader\Guzzle($guzzle);

$proxyServer = new \Zoya\ProxyServer('http://8.8.8.8:8080');
$proxiesList = [$proxyServer];

$identity = new \Zoya\Loader\ChangeIdentity\Random();

$proxy = new \Zoya\Proxy($identity, $proxiesList);
$loaderWithProxy = new \Zoya\ProxySwitcher\GuzzleSwitcher($loader, $proxy, $guzzle);

$resource = new \Valera\Resource('http://google.com', null, \Valera\Resource::METHOD_GET );
$result = new \Valera\Loader\Result();

$loaderWithProxy->load($resource, $result);


$torProxy = new \Zoya\TorProxy($identity, [new Zoya\ProxyServer('127.0.0.1:8118')]);

$loaderWithTorProxy = new \Zoya\ProxySwitcher\GuzzleSwitcher($loader, $torProxy, $guzzle);

$result = new \Valera\Loader\Result();

$loaderWithProxy->load($resource, $result);

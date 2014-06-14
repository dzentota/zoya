<?php

use Zoya\ProxySwitcher;

$guzzle = new Guzzle\Http\Client();
$loader = new Valera\Loader\Guzzle($guzzle);

$proxyServer = new Zoya\ProxyServer('http://8.8.8.8:8080');
$proxiesList = [$proxyServer];

$identity = new \Zoya\Loader\ChangeIdentity\Random($proxiesList);

$proxy = new ProxySwitcher($loader, $guzzle, $proxiesList);

$resource = new \Valera\Resource('http://google.com', null, \Valera\Resource::METHOD_GET );
$result = new \Valera\Loader\Result();

$proxy->load($resource, $result);
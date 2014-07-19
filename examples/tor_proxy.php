<?php

require __DIR__ . '/../vendor/autoload.php';

$guzzle = new \GuzzleHttp\Client(['defaults'=> ['timeout'=>3]]);
$loader = new \Valera\Loader\Guzzle($guzzle);

$proxiesList = new Zoya\ProxyList(['socks5://127.0.0.1:9150']);

$coin = new \Zoya\Coin\Always();

$proxy = new \Zoya\TorProxy($coin, $proxiesList);
$proxy->setCookieFileName('/usr/bin/tor-browser/Data/Tor/control_auth_cookie');

$loaderWithProxy = new \Zoya\ProxySwitcher\GuzzleSwitcher($loader, $proxy, $guzzle);

$resource = new \Valera\Resource('https://www.whatismyip.com/', null, \Valera\Resource::METHOD_GET );
$result = new \Valera\Loader\Result();

$loaderWithProxy->load($resource, $result);
file_put_contents('/tmp/myip', $result->getContent());

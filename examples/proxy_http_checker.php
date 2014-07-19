<?php
require __DIR__ . '/../vendor/autoload.php';

$proxyServer = new \Zoya\ProxyServer('http://200.84.3.227:8080');

$checker = new Zoya\HttpProxyChecker($proxyServer, 'http://web-customize.com/');

var_dump($checker->check());

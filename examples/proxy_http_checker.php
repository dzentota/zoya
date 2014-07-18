<?php
require __DIR__ . '/../vendor/autoload.php';

$proxyServer = new \Zoya\ProxyServer('http://110.169.100.27:8080');

$checker = new Zoya\HttpProxyChecker($proxyServer, 'http://web-customize.com/');

var_dump($checker->check());

<?php

require __DIR__ . '/../vendor/autoload.php';

$loader = new \Zoya\Loader\Phantomjs();

$resource = new \Valera\Resource('http://google.com', null, \Valera\Resource::METHOD_GET );

$result = new \Valera\Loader\Result();
$loader->load($resource, $result);

var_dump($result);

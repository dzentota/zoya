<?php

require __DIR__ . '/../vendor/autoload.php';

$loader = new \Zoya\Loader\Phantomjs();

//1 second delay
$delayedLoader = new \Zoya\Loader\Delayed($loader, ['delay'=>1000000]);

$resource = new \Valera\Resource('http://google.com', null, \Valera\Resource::METHOD_GET );

$result = new \Valera\Loader\Result();
$delayedLoader->load($resource, $result);

var_dump($result);

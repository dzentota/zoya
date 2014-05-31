<?php

require __DIR__ . '/../vendor/autoload.php';

$loader = new \Zoya\Loader\Phantomjs();
$resource = new \Valera\Resource('http://auto.onliner.by/2014/05/31/vaz-21144', null, \Valera\Resource::METHOD_GET );
$result = new \Valera\Loader\Result();

$loader->load($resource, $result);
$html = $result->getContent();

$source = new \Valera\Source('test', $resource);
$content = new \Valera\Content($html, 'text/html', $source);
$parser = new \Zoya\Parser\Readability();

$result = new Valera\Parser\Result();
$parser->parse($content, $result);

print_r($result->getNewDocuments());

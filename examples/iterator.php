<?php

$arr = new \InfiniteIterator(new \ArrayIterator(['first', 'second']));
$arr->rewind();
//echo current($arr), PHP_EOL;
echo $arr->current(), PHP_EOL;
//
$arr->next();
echo $arr->current();
//$arr = ['a', 'b'];
//
//echo current($arr);
//
//echo next($arr);
//echo current($arr);
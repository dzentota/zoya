<?php

namespace Zoya\Diff;

class Mapper
{
    private $map;
    private $document;

    public function __construct($document, array $map=[])
    {
        $this->document = $document;
        $this->map = $map;
    }

    public function map($key)
    {
        $method = $this->getMethodName($key);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        if (isset($this->map[$key])) {
            return [$this->map[$key] => $this->getDocumentKey($key)];
        }
        throw new \Exception('No mapping found');
    }

    protected function getDocumentKey($key)
    {
        return $this->document->$key;
    }

    protected function getMethodName($key)
    {
        return 'map' . $this->getMethodName($key);

    }


    protected function camelize($str)
    {
        $str[0] = strtoupper($str[0]);
        return preg_replace_callback('/[_-]([a-z])/', function ($c) {
            return strtoupper($c[1]);
        }, $str);
    }

}

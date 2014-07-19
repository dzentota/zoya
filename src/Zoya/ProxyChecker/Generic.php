<?php

namespace Zoya\ProxyChecker;

use Valera\Loader\Result;
use Valera\Resource;
use Zoya\ProxySwitcher\ProxyInterface;

/**
 * Class ProxyChecker
 * @package Zoya
 */
class Generic
{
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var Resource
     */
    private $resource;
    /**
     * @var ProxyInterface
     */
    private $proxyLoader;

    /**
     * @param ProxyInterface $proxyLoader
     * @param \Valera\Resource $resource
     * @param callable $callback
     */
    public function __construct(ProxyInterface $proxyLoader, Resource $resource, callable $callback = null)
    {
        $this->proxyLoader = $proxyLoader;
        $this->resource = $resource;
        $this->callback = $callback;
    }

    /**
     * @return bool
     */
    public function check()
    {
        $result = new Result();
        $this->getProxyLoader()
            ->applyProxy()
            ->getLoader()//Don't switch identity
            ->load($this->resource, $result);

        if (null !== $this->callback) {
            $callback = $this->callback;
            if (!$callback($result->getContent())) {
                return false;
            } else {
                return true;
            }
        }
        return (bool)$this->callDefaultCallback($result->getContent());
    }

    /**
     * @param $content
     * @return int
     */
    public function callDefaultCallback($content)
    {
        return stripos($content, '</html>');
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return \Zoya\ProxySwitcher\ProxyInterface
     */
    public function getProxyLoader()
    {
        return $this->proxyLoader;
    }


    /**
     * @return ProxyServer
     */
    public function getProxyServer()
    {
        return $this->getProxyLoader()
            ->getProxyServer();
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }
}

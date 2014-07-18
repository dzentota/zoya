<?php

namespace Zoya\ProxySwitcher;

use Valera\Loader\LoaderInterface;
use Valera\Loader\Result;
use Valera\Resource;
use Zoya\Proxy;
use Zoya\ProxySwitcher\ProxyInterface;

/**
 * Class Generic
 * @package Zoya\ProxySwitcher
 */
abstract class Generic implements LoaderInterface, ProxyInterface
{

    /**
     * @var \Valera\Loader\LoaderInterface
     */
    private $loader;

    /**
     * @var null
     */
    private $client;

    /**
     * @var \Zoya\Proxy
     */
    private $proxy;

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @return mixed
     */
    public function getProxyServer()
    {
        return $this->proxy->getProxy();
    }

    /**
     * @return Proxy
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @param LoaderInterface $loader
     * @param Proxy $proxy
     * @param null $client
     */
    public function __construct(LoaderInterface $loader, Proxy $proxy, $client = null)
    {
        $this->loader = $loader;
        $this->client = $client;
        $this->proxy = $proxy;

    }

    /**
     * @param \Valera\Resource $resource
     * @param Result $result
     */
    public function load(Resource $resource, Result $result)
    {
        $this->switchProxy();
        $this->getLoader()->load($resource, $result);
        $this->getProxy()->switchIdentity();
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array(array($this->getLoader(), $method), $params);
    }

    /**
     * @return mixed
     */
    abstract public function switchProxy();

}

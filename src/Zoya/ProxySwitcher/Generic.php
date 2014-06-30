<?php

namespace Zoya\ProxySwitcher;

use Valera\Loader\LoaderInterface;
use Valera\Loader\Result;
use Valera\Resource;
use Zoya\Proxy;
use Zoya\ProxySwitcher\SwitchableInterface;

abstract class Generic implements LoaderInterface, SwitchableInterface
{

    private $loader;

    private $client;

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

    public function getProxy()
    {
        return $this->proxy;
    }

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
        return call_user_func_array(array($this->loader, $method), $params);
    }

    abstract public function switchProxy();

}

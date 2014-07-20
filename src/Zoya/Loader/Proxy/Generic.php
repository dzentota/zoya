<?php

namespace Zoya\Loader\Proxy;

use Valera\Loader\LoaderInterface;
use Valera\Loader\Result;
use Valera\Resource;
use Zoya\ProxySwitcher\SwitcherInterface;

abstract class Generic implements LoaderInterface, ProxyInterface
{

    /**
     * @var \Valera\Loader\LoaderInterface
     */
    private $loader;

    /**
     * @var SwitcherInterface
     */
    private $switcher;

    /**
     * @param LoaderInterface $loader
     * @param SwitcherInterface $switcher
     */
    public function __construct(LoaderInterface $loader, SwitcherInterface $switcher)
    {
        $this->loader = $loader;
        $this->switcher = $switcher;

    }

    /**
     * @param \Valera\Resource $resource
     * @param Result $result
     */
    public function load(Resource $resource, Result $result)
    {
        $this->applyProxy();
        $this->getLoader()->load($resource, $result);
        $this->getSwitcher()->switchProxy();
    }

    /**
     * @return \Zoya\ProxySwitcher\SwitcherInterface
     */
    public function getSwitcher()
    {
        return $this->switcher;
    }

    /**
     * @return \Valera\Loader\LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
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
    abstract public function applyProxy();

}

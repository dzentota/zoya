<?php

namespace Zoya\ProxySwitcher;

use Zoya\InfiniteList;
use Zoya\ProxySwitcher\ProxyInterface;

/**
 * Class Generic
 * @package Zoya\ProxySwitcher
 */
class Generic implements SwitcherInterface
{

    private $proxies;

    /**
     * @param InfiniteList $proxies
     */
    public function __construct(InfiniteList $proxies)
    {
        $this->proxies = $proxies;
    }

    /**
     * @return \InfiniteIterator
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    /**
     * @return mixed
     */
    public function getProxy()
    {
        $proxy = $this->getProxies()->current();
        return $proxy;
    }

    public function switchProxy()
    {
        $this->getProxies()->next();
    }

}

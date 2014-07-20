<?php

namespace Zoya;

class Proxy
{
    private $proxies;

    /**
     * @param InfiniteList $proxies
     */
    public function __construct(InfiniteList $proxies )
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

    /**
     * Switch the proxy if needed
     */
    public function switchIdentity()
    {
        $this->getCoin()->flip();
        if ($this->getCoin()->isLucky()) {
            $this->switchProxy();
        }
    }

    public function switchProxy()
    {
        $this->getProxies()->next();
    }
}

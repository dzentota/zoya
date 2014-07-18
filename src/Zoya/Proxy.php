<?php

namespace Zoya;

use Assert\Assertion;
use Zoya\Coin\CoinInterface;

class Proxy
{
    private $proxies;
    private $coin;

    /**
     * @param \Zoya\Coin\CoinInterface $coin
     * @param \Zoya\ProxyList $proxies
     */
    public function __construct(CoinInterface $coin, ProxyList $proxies )
    {
        $this->coin = $coin;
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
     * @return CoinInterface
     */
    public function getCoin()
    {
        return $this->coin;
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

    protected function switchProxy()
    {
        $this->getProxies()->next();
    }
}

<?php

namespace Zoya;

use Assert\Assertion;
use Zoya\Loader\ChangeIdentityInterface;

class Proxy
{
    private $proxies;
    private $identity;

    /**
     * @param ChangeIdentityInterface $identity
     * @param array $proxies
     */
    public function __construct(ChangeIdentityInterface $identity, array $proxies=[] )
    {
        Assertion::notEmpty($proxies, 'At least on proxy expected');
        $this->identity = $identity;
        $this->proxies = new \InfiniteIterator(new \ArrayIterator($proxies));
        $this->proxies->rewind();
    }

    /**
     * @return \InfiniteIterator
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    /**
     * @return ChangeIdentityInterface
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return mixed
     */
    public function getProxy()
    {
        $proxy = $this->proxies->current();
        return $proxy;
    }

    /**
     * Switch the proxy if needed
     */
    public function switchIdentity()
    {
        if ($this->getIdentity()->changeIdentity()) {
            $this->getProxies()->next();
        }
    }
}

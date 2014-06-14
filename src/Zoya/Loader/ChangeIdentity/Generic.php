<?php

namespace Zoya\Loader\ChangeIdentity;

use Zoya\Loader\ChangeIdentityInterface;

abstract class Generic implements ChangeIdentityInterface
{
    protected $proxies;

    public function __construct(array $proxies = [])
    {
        $this->proxies = new \InfiniteIterator(new \ArrayIterator($proxies));
        $this->proxies->rewind();
    }

    public function getIdentity()
    {
        return $this->proxies->current();
    }

    abstract public function changeIdentity();
}

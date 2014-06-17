<?php

namespace Zoya;

class ProxySwitcher extends GenericProxySwitcher
{
    protected function changeIdentity()
    {
        $this->getProxies()->next();
        $this->getLoaderAdapter($this->getLoader());
    }
}

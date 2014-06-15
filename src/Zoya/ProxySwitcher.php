<?php

namespace Zoya;

class ProxySwitcher extends GenericProxySwitcher
{
    protected function changeIdentity()
    {
        $config = $this->getClient()->getConfig();
        $this->getProxies()->next();
        $proxy = $this->getProxies()->current();
        $config[Client::REQUEST_OPTIONS]['proxy'] = $proxy->getServer();
        $this->getClient()->setConfig($config);
    }
}

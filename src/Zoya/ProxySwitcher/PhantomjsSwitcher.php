<?php

namespace Zoya\ProxySwitcher;

class PhantomjsSwitcher extends Generic
{

    public function switchProxy()
    {
        $proxy = $this->getProxyServer();
        $loader = $this->getLoader();
        $config['proxy-type'] = $proxy->getType();
        $config['proxy'] = $proxy->getPort()? $proxy->getHost() . ':' . $proxy->getPort()
            : $proxy->getHost();
        $config['proxy-auth'] = $proxy->getPassword()? $this->getUser() . ':' . $proxy->getPassword()
            : $proxy->getUser();
        $loader->addCliOptions(
            array_filter($config, function($opt) {
                return !empty($opt);
            })
        );
    }
}

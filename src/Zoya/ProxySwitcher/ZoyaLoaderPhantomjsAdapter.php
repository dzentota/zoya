<?php

namespace Zoya\ProxySwitcher;

class ZoyaLoaderPhantomjsAdapter extends GenericAdapter
{

    public function switchProxy()
    {
        $proxy = $this->getSwitcher()->getProxies()->current();
        $loader = $this->getSwitcher()->getLoader();
        $config['proxy-type'] = $proxy->getType();
        $config['proxy'] = $proxy->getPort()? $proxy->getHost() . ':' . $proxy->getProxy
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

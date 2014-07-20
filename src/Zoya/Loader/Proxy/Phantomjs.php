<?php

namespace Zoya\Loader\Proxy;

use Zoya\ProxySwitcher\SwitcherInterface;

class Phantomjs extends Generic
{
    public function __construct(\Zoya\Loader\Phantomjs $loader, SwitcherInterface $switcher)
    {
        parent::__construct($loader, $switcher);
    }

    /**
     * @return mixed
     */
    public function applyProxy()
    {
        $proxy = $this->getSwitcher()->getProxy();
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
        return $this;
    }
}

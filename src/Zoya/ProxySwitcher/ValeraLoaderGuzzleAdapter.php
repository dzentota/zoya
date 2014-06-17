<?php

namespace Zoya\ProxySwitcher;

use Guzzle\Http\Client;

class ValeraLoaderGuzzleAdapter extends GenericAdapter
{

    public function switchProxy()
    {
        $client = $this->getSwitcher()->getClient();
        $config = $client->getConfig();
        $proxy = $this->getSwitcher()->getProxies()->current();
        $config[Client::REQUEST_OPTIONS]['proxy'] = $proxy->getServer();
        $client->setConfig($config);
    }
}
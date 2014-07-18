<?php

namespace Zoya\ProxySwitcher;

use GuzzleHttp\Client;
use Valera\Loader\LoaderInterface;
use Zoya\Proxy;
use Zoya\ProxyServer;

class GuzzleSwitcher extends Generic
{

    /**
     * Override parent constructor for Guzzle\Http\Client type hint
     * @param LoaderInterface $loader
     * @param Proxy $proxy
     * @param Client $client
     */
    public function __construct(LoaderInterface $loader, Proxy $proxy, Client $client)
    {
        parent::__construct($loader, $proxy, $client);
    }

    /**
     * Switch proxy
     */
    public function switchProxy()
    {
        $client = $this->getClient();
        $proxy = $this->getProxyServer();
        $client->setDefaultOption('proxy', $proxy->getServer());

    }
}

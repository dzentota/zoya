<?php

namespace Zoya\ProxySwitcher;

use Guzzle\Http\Client;
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
        $config = $client->getConfig();
        $proxy = $this->getProxyServer();
        $proxyConfig[Client::REQUEST_OPTIONS]['proxy'] = $proxy->getServer();
        if ($proxy->getType() == ProxyServer::TYPE_SOCKS5) {
            $proxyConfig[Client::CURL_OPTIONS][CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
        }
        $config->overwriteWith($proxyConfig);
        $client->setConfig($config);
    }
}

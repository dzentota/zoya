<?php

namespace Zoya\Loader\Proxy;

use GuzzleHttp\Client;
use Valera\Loader\LoaderInterface;
use Zoya\ProxySwitcher\SwitcherInterface;

class Guzzle extends Generic
{
    private $client;

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /***
     * @param \Valera\Loader\Guzzle $loader
     * @param SwitcherInterface $switcher
     * @param Client $client
     */
    public function __construct(\Valera\Loader\Guzzle $loader, SwitcherInterface $switcher, Client $client)
    {
        parent::__construct($loader, $switcher);
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function applyProxy()
    {
        $client = $this->getClient();
        $proxy = $this->getSwitcher()->getProxy();
        $client->setDefaultOption('proxy', $proxy->getServer());
        return $this;
    }
}
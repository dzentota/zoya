<?php

namespace Zoya;

use Guzzle\Http\Client;
use Valera\Loader\LoaderInterface;
use Valera\Loader\Result;
use Valera\Resource;
use Zoya\Loader\ChangeIdentityInterface;

class ProxySwitcher implements LoaderInterface
{
    private $loader;

    private $client;

    private $identity;

    public function __construct(LoaderInterface $loader, $client, ChangeIdentityInterface $identity)
    {
        $this->loader = $loader;
        $this->client = $client;
        $this->identity = $identity;
    }

    public function load(Resource $resource, Result $result)
    {
        $this->loader->load($resource, $result);

        $config = $this->client->getConfig();
        $config[Client::REQUEST_OPTIONS]['proxy'] = $this->identity->getIdentity()->getServer();
        $this->client->setConfig($config);

        $this->identity->changeIdentity();

    }
}

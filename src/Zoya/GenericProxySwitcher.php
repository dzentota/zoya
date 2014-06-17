<?php

namespace Zoya;

use Symfony\Component\Yaml\Exception\RuntimeException;
use Valera\Loader\LoaderInterface;
use Valera\Loader\Result;
use Valera\Resource;
use Zoya\Loader\ChangeIdentityInterface;
use Zoya\ProxySwitcher\AdapterInterface;

abstract class GenericProxySwitcher implements LoaderInterface
{

    private $loader;

    private $client;

    private $identity;

    private $proxies;

    public function getClient()
    {
        return $this->client;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    public function getProxies()
    {
        return $this->proxies;
    }

    public function __construct(LoaderInterface $loader, $client, array $proxies=[], ChangeIdentityInterface $identity)
    {
        $this->loader = $loader;
        $this->client = $client;

        $this->proxies = new \InfiniteIterator(new \ArrayIterator($proxies));
        $this->proxies->rewind();

        $this->identity = $identity;
    }

    public function load(Resource $resource, Result $result)
    {
        $this->getLoader()->load($resource, $result);
        if ($this->getIdentity()->changeIdentity()) {
            $this->changeIdentity();
        }
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array(array($this->loader, $method), $params);
    }

    abstract protected function changeIdentity();

    /**
     * @param LoaderInterface $loader
     * @return AdapterInterface
     * @throws \RuntimeException
     */
    public function getLoaderAdapter(LoaderInterface $loader)
    {
        $adapterClassName = '\Zoya\ProxySwitcher\\' . str_replace('\\', '', get_class($loader)) . 'Adapter';
        if (class_exists($adapterClassName)) {
            return new $adapterClassName($this);
        }
        throw new \RuntimeException(
            sprintf('Adapter for class %s not found', get_class($loader))
        );
    }

}

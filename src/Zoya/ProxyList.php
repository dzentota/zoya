<?php

namespace Zoya;

use Assert\Assertion;

/**
 * Class ProxyList
 * @package Zoya
 */
class ProxyList
{
    /**
     * @var array
     */
    private $proxies;
    /**
     * @var
     */
    private $list;

    /**
     * @param mixed $pathOrList Path to file with list of proxies or array with proxies, e.g. ['http://127.0.0.1:80']
     * @throws \InvalidArgumentException
     */
    public function __construct($pathOrList)
    {
        if (is_array($pathOrList)) {
            $proxiesList = $pathOrList;
        } else {
            Assertion::file($pathOrList);
            $proxiesList = file($pathOrList);
        }
        $proxiesList = array_unique(array_filter($proxiesList));
        Assertion::notEmpty($proxiesList, 'At least one proxy expected');
        $proxies = [];
        foreach ($proxiesList as $proxy) {
            if (false !== filter_var($proxy, FILTER_VALIDATE_URL)) {
                $proxies[] = new ProxyServer($proxy);
            } else {
                throw new \InvalidArgumentException(sprintf('Proxy should be a valid URL. %s given', $proxy));
            }
        }
        $this->proxies = $proxies;
        $this->init();

    }


    /**
     * @param $proxy prepends $proxy to the list.
     */
    public function add($proxy)
    {
        if (!is_array($proxy)) {
            $proxy = [$proxy];
        }
        $newElements = new \ArrayIterator($proxy);
        $combinedIterator = new \AppendIterator();
        $combinedIterator->append($newElements);
        $combinedIterator->append($this->list);
        $this->list = $combinedIterator;
    }

    /**
     * @param ProxyServer $proxy
     * @return $this
     */
    public function prepend(ProxyServer $proxy)
    {
        array_unshift($this->proxies, $proxy);
        return $this;
    }

    /**
     * @param array $proxies
     * @return $this
     */
    public function prependList(array $proxies)
    {
        foreach ($proxies as $proxy) {
            $this->prepend($proxy);
        }
        return $this;
    }

    /**
     * @param ProxyServer $proxy
     * @return $this
     */
    public function append(ProxyServer $proxy)
    {
        array_push($this->proxies, $proxy);
        return $this;
    }

    /**
     * @param array $proxies
     * @return $this
     */
    public function appendList(array $proxies)
    {
        foreach ($proxies as $proxy) {
            $this->append($proxy);
        }
        return $this;
    }

    /**
     * Init infinite iterator with new elements
     */
    public function init()
    {
        $this->list = new \InfiniteIterator(new \ArrayIterator($this->getProxies()));
        $this->list->rewind();
    }

    /**
     * @return array
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array(array($this->list, $method), $params);
    }
}

<?php

namespace Zoya\Loader;
use Valera\Loader\LoaderInterface;
use Valera\Loader\Result;
use Valera\Resource;

class Delayed implements LoaderInterface
{

    /**
     * @var \Valera\Loader\LoaderInterface
     */
    protected $loader;
    /**
     * @var int delay between requests in microseconds
     */
    protected $delay;

    /**
     * @var int Default delay between requests in microseconds
     */
    const DEFAULT_DELAY = 100000;

    public function __construct(LoaderInterface $loader, $delay = self::DEFAULT_DELAY)
    {
        $this->loader = $loader;
        $this->delay = $delay;
    }

    /**
     * @param \Valera\Resource $resource
     * @param \Valera\Loader\Result $result
     * @return mixed
     */
    public function load(Resource $resource, Result $result)
    {
        $response = $this->loader->load($resource, $result);
        $this->makeDelay();
        return $response;
    }

    /**
     * @param $delay
     * @throws \InvalidArgumentException
     */
    public function setDelay($delay)
    {
         $delay = filter_var($delay, FILTER_VALIDATE_INT, ['min_range'=>10000]);
         if (false !== $delay) {
            $this->delay = $delay;
         } else {
             throw new \InvalidArgumentException('Delay should be natural number not less than 10000');
         }
    }

    /**
     * @return mixed
     */
    public function getDelay()
    {
        return isset($this->config['delay'])? $this->config['delay'] : self::DEFAULT_DELAY;
    }

    /**
     *
     */
    protected function makeDelay()
    {
        $delay = $this->getDelay();
        usleep($delay);
    }

    /**
     * Proxy
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array(array($this->loader, $method), $params);
    }
}

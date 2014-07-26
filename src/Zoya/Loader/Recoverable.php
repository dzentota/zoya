<?php

namespace Zoya\Loader;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Valera\Loader\LoaderInterface;
use Valera\Loader\Result;
use Valera\Resource;
use Zoya\ProxyChecker;

class Recoverable implements LoaderInterface
{

    /**
     * @var \Valera\Loader\LoaderInterface
     */
    protected $loader;
    /**
     * @var int delay between requests in microseconds
     */
    protected $delay;

    protected $checker;
    /**
     * @var int Default delays between requests in microseconds
     */
    protected $defaultDelays = [1000000, 2000000,5000000];

    protected $tries = 0;

    public function __construct(LoaderInterface $loader, array $delays = [], ProxyChecker $checker = null)
    {
        $this->loader = $loader;
        $this->delays = empty($delays)? $this->defaultDelays : $delays;
        $this->checker = $checker;
    }

    public function getMaxTries()
    {
        return count($this->delays) + 1;
    }

    public function getDelay($try)
    {
        return isset($this->delays[$try])? $this->delays[$try] : 0;
    }

    /**
     * @param \Valera\Resource $resource
     * @param \Valera\Loader\Result $result
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\BadResponseException
     * @return mixed
     */
    public function load(Resource $resource, Result $result)
    {
        $cloned = clone $result;
        $response = null;
        try {
            $this->tries++;
            $response = $this->loader->load($resource, $result);
            $this->tries = 0;
        } catch (BadResponseException $e) {
            return $this->reload($resource, $cloned, $e);
        } catch (RequestException $e) {
            if (strpos($e->getMessage(), 'Connection timed out')) {
                return $this->reload($resource, $cloned, $e);
            }
        }
        return $response;
    }


    /**
     *
     */
    protected function makeDelay($delay)
    {
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

    /**
     * @param \Valera\Resource $resource
     * @param $cloned
     * @param $e
     * @throws
     * @return mixed
     */
    protected function reload(Resource $resource, $cloned, $e)
    {
        if ($this->tries < $this->getMaxTries()) {
            if (in_array('Zoya\Loader\Proxy\ProxyInterface', class_implements($this->loader))) {
                try {
                    if (!$this->checker->check()) {//Proxy is OK, retry after timeout
                        $this->loader->getSwitcher()->getProxies()->removeCurrentItem();
                    }
                } catch (RequestException $e) {//possibly timed out
                    $this->loader->getSwitcher()->getProxies()->removeCurrentItem();
                }

            }
            $this->makeDelay($this->getDelay($this->tries));
            return $this->load($resource, $cloned);
        } else {
            throw $e;
        }
    }
}

<?php

namespace Zoya;

class ProxyChecker
{
    private $callback;
    private $proxyServer;

    /**
     * @param ProxyServer $proxyServer
     * @param callable $callback
     */
    public function __construct(ProxyServer $proxyServer, callable $callback = null)
    {
        $this->proxyServer = $proxyServer;
        $this->callback = $callback;
    }

    /**
     * @param $data
     * @return bool
     */
    public function check($data)
    {
        if (null !== $this->callback) {
            $callback = $this->callback;
            if (!$callback($data)) {
                $this->proxyServer->setStatus(ProxyServer::STATUS_DEAD);
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }
}

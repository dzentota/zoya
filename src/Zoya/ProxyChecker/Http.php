<?php

namespace Zoya\ProxyChecker;

/**
 * Class ProxyChecker
 * @package Zoya
 */
class Http
{
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var
     */
    private $url;
    /**
     * @var array
     */
    private $options;
    /**
     * @var ProxyServer
     */
    private $proxyServer;

    /**
     * @param ProxyServer $proxyServer
     * @param $url
     * @param array $options
     * @param callable $callback
     * @throws \InvalidArgumentException
     */
    public function __construct(ProxyServer $proxyServer, $url, array $options = [], callable $callback = null)
    {
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Valid URL expected');
        }
        $this->proxyServer = $proxyServer;
        $this->url = $url;
        $this->options = $options;
        $this->callback = $callback;
    }

    /**
     * @return bool
     */
    public function check()
    {
        $options = $this->getOptions();
        $scheme = $this->getProxyServer()->getScheme() ? : 'http';
        $server = $this->proxyServer->getServer();

        $options[$scheme]['proxy'] = str_replace($scheme . '://', 'tcp://', $server);
        $options[$scheme]['request_fulluri'] = true;

        $context = stream_context_create($options);
        $content = file_get_contents($this->getUrl(), false, $context);
        if (null !== $this->callback) {
            $callback = $this->callback;
            if (!$callback($content)) {
                return false;
            } else {
                return true;
            }
        }
        return (bool)$this->callDefaultCallback($content);
    }

    /**
     * @param $content
     * @return int
     */
    public function callDefaultCallback($content)
    {
        return stripos($content, '</html>');
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return ProxyServer
     */
    public function getProxyServer()
    {
        return $this->proxyServer;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
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

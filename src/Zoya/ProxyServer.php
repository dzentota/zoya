<?php

namespace Zoya;

use Assert\Assertion;

/**
 * Class ProxyServer
 * @package Zoya
 */
class ProxyServer
{
    /**
     * @var
     */
    private $server;
    /**
     * @var
     */
    private $scheme;
    /**
     * @var
     */
    private $host;

    /**
     * @var
     */
    private $port;
    /**
     * @var
     */
    private $user;
    /**
     * @var
     */
    private $pass;
    /**
     * @var
     */
    private $path;
    /**
     * @var
     */
    private $query;
    /**
     * @var
     */
    private $fragment;

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }


    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @param $server
     */
    public function __construct($server)
    {
        //Do not use Assert::url because it don't support username@password in URL
        if (false !== filter_var($server, FILTER_VALIDATE_URL)) {
            $data = parse_url($server);
            $this->server = $server;
            foreach ($data as $param=>$value) {
                $this->$param = $value;
            }
        } else {
            throw new \InvalidArgumentException('Valid URL expected');
        }
    }

}

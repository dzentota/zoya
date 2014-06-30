<?php

namespace Zoya;

use Assert\Assertion;

/**
 * Class ProxyServer
 * @package Zoya
 */
class ProxyServer
{
    const TYPE_HTTP = 'http';
    const TYPE_HTTPS = 'https';
    const TYPE_SOCKS5 = 'socks5';

    const STATUS_UNKNOWN = 0;
    const STATUS_ALIVE = 1;
    const STATUS_DEAD = 2;

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
     * @var
     */
    private $type;

    /**
     * @var int
     */
    private $status;
    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        Assertion::inArray($status, [self::STATUS_UNKNOWN, self::STATUS_ALIVE, self::STATUS_DEAD]);
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status? : self::STATUS_UNKNOWN;
    }
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

    /**
     * @return mixed
     */
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
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        Assertion::inArray($type, [self::TYPE_HTTP, self::TYPE_HTTPS, self::TYPE_SOCKS5]);
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $type = $this->type? : $this->getScheme();
        return $type?: self::TYPE_HTTP;
    }


    /**
     * @param $server
     * @throws \InvalidArgumentException
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

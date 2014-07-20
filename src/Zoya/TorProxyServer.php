<?php

namespace Zoya;

class TorProxyServer extends ProxyServer
{
    const AUTH_METHOD_NOT_SET = -1;
    const AUTH_METHOD_NULL = 0;
    const AUTH_METHOD_HASHEDPASSWORD = 1;
    const AUTH_METHOD_COOKIE = 2;

    /**
     * e.g. /usr/bin/tor-browser/Data/Tor/control_auth_cookie
     * @var path to TOR cookie file
     */
    private $cookieFileName;
    /**
     * @var string Tor control port to send commands to. Defaults to 9151
     */
    private $controlPort = '9151';

    private $hashedPassword;

    private $authMethod = self::AUTH_METHOD_NOT_SET;

    private $timeout = -1;

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }


    /**
     * @param mixed $authMethod
     * @return $this
     */
    public function setAuthMethod($authMethod)
    {
        $this->authMethod = $authMethod;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthMethod()
    {
        return $this->authMethod;
    }

    /**
     * @param mixed $hashedPassword
     * @return $this
     */
    public function setHashedPassword($hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHashedPassword()
    {
        return $this->hashedPassword;
    }


    /**
     * @param string $controlPort
     * @return $this
     */
    public function setControlPort($controlPort)
    {
        $this->controlPort = $controlPort;
        return $this;
    }

    /**
     * @return string
     */
    public function getControlPort()
    {
        return $this->controlPort;
    }

    /**
     * @return mixed
     */
    public function getCookieFileName()
    {
        return $this->cookieFileName;
    }

    /**
     * @param $fileName
     * @return $this
     */
    public function setCookieFileName($fileName)
    {
        $this->cookieFileName = $fileName;
        return $this;
    }

}

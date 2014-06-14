<?php

namespace Zoya\Loader;

/**
 * Class Tor
 * @package Zoya\Loader
 */
class Tor implements ChangeIdentityInterface
{

    /**
     * @var LoaderInterface
     */
    protected $loader;
    /**
     * @var string
     */
    protected $ip;
    /**
     * @var string
     */
    protected $port;
    /**
     * @var string
     */
    protected $password;
    /**
     * @var
     */
    protected $cookieFileName;

    /**
     * @param LoaderInterface $loader
     * @param string $ip
     * @param string $port (9051)
     * @param string $password
     */
    public function __construct(LoaderInterface $loader, $ip = '127.0.0.1', $port = '8118', $password='')
    {
        $this->loader = $loader;
        $this->ip = $ip;
        $this->port = $port;
        $this->password = $password;
    }

    /**
     * @param $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param $loader
     * @return $this
     */
    public function setLoader($loader)
    {
        $this->loader = $loader;
        return $this;
    }

    /**
     * @return LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getCookieFileName()?
            $this->getCookie() : $this->password;
    }

    /**
     * @return bool
     * @throws \RuntimeException
     */
    public function getNewIdentity()
    {
        $fp = fsockopen($this->getIp(), $this->getPort(), $errno, $errstr, 30);
        if (!$fp) {
            throw new \RuntimeException("Can't connect to the control port. " . $errstr);
        }
        fputs($fp, "AUTHENTICATE ". $this->getPassword()."\r\n");
        $response = fread($fp, 1024);
        list($code, $text) = explode(' ', $response, 2);
        if ($code != '250') {
            throw new \RuntimeException('Tor authentication failed');
        }
        //send the request to for new identity
        fputs($fp, "signal NEWNYM\r\n");
        $response = fread($fp, 1024);
        list($code, $text) = explode(' ', $response, 2);
        if ($code != '250') {
            throw new \RuntimeException('Tor signal failed');
        } //signal failed
        fclose($fp);
        return true;
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

    /**
     * Load the TOR's cookie from a file and encode it in hexadecimal.
     **/
    function getCookie()
    {
        $filename = $this->getCookieFileName();
        $cookie = file_get_contents($filename);
        //convert the cookie to hexadecimal
        $hex = '';
        for ($i=0;$i<strlen($cookie);$i++){
            $h = dechex(ord($cookie[$i]));
            $hex .= str_pad($h, 2, '0', STR_PAD_LEFT);
        }
        return strtoupper($hex);
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
}
<?php

namespace Zoya;

use Assert\Assertion;
use Valera\Resource;

class TorProxySwitcher extends GenericProxySwitcher
{
    private $ip = '127.0.0.1';
    private $port = '8118';
    private $password = '';
    private $cookieFileName;

    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword()
    {
        return $this->getCookieFileName()?
            $this->getCookie() : $this->password;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function getPort()
    {
        return $this->port;
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

    protected function changeIdentity()
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
}

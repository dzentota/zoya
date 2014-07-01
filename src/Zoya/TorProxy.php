<?php

namespace Zoya;

use Assert\Assertion;

/**
 *
 * Class TorProxy
 * @package Zoya\ProxySwitcher
 */
class TorProxy extends Proxy
{
    private $cookieFileName;


    public function getPassword()
    {
        return $this->getCookieFileName()?
            $this->getCookie() : $this->getProxy()->getPassword();
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
     * Send signal to TOR to change identity
     * @return bool
     * @throws \RuntimeException
     */
    public function switchProxy()
    {
        $fp = fsockopen($this->getProxy()->getIp(), $this->getProxy()->getPort(), $errno, $errstr, 30);
        if (!$fp) {
            throw new \RuntimeException("Can't connect to the control port. " . $errstr);
        }
        fputs($fp, "AUTHENTICATE ". $this->getProxy()->getPassword()."\r\n");
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
     *
     */
    public function switchIdentity()
    {
        if ($this->getIdentity()->changeIdentity()) {
            $this->getProxies()->next();
            $this->switchProxy();
        }
    }
}

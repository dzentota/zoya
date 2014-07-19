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
    private $cookieFileName; // = '/usr/bin/tor-browser/Data/Tor/control_auth_cookie';
    /**
     * @var string Tor control port to send commands to. Defaults to 9151
     */
    private $controlPort = '9151';

    /**
     * @param string $controlPort
     */
    public function setControlPort($controlPort)
    {
        $this->controlPort = $controlPort;
    }

    /**
     * @return string
     */
    public function getControlPort()
    {
        return $this->controlPort;
    }


    public function getPassword()
    {
        return $this->getCookieFileName() ?
            $this->getCookie() : $this->getProxy()->getPassword();
    }

    /**
     * e.g. /usr/bin/tor-browser/Data/Tor/control_auth_cookie
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
    public function getCookie()
    {
        $filename = $this->getCookieFileName();
        Assertion::file($filename, 'TOR cookie file not found');
        $cookie = file_get_contents($filename);
        //convert the cookie to hexadecimal
        $hex = '';
        for ($i = 0; $i < strlen($cookie); $i++) {
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
    protected function switchProxy()
    {
        $this->getProxies()->next();
        $fp = fsockopen($this->getProxy()->getHost(), $this->getControlPort(), $errno, $errstr, 30);
        if (!$fp) {
            throw new \RuntimeException("Can't connect to the control port. " . $errstr);
        }
        fputs($fp, 'AUTHENTICATE ' . $this->getPassword() . "\r\n");
        $response = fread($fp, 1024);
        if (empty($response)) {
            throw new \RuntimeException('Tor authentication failed. Possibly wrong control port.');
        }
        list($code, $text) = explode(' ', $response, 2);
        if ($code != '250') {
            throw new \RuntimeException('Tor authentication failed: ' . $response);
        }
        //send the request to for new identity
        fputs($fp, "signal NEWNYM\r\n");
        $response = fread($fp, 1024);
        list($code, $text) = explode(' ', $response, 2);
        if ($code != '250') {
            throw new \RuntimeException('Tor signal failed: ' . $response);
        } //signal failed
        fclose($fp);
        return true;
    }

}

<?php

namespace Zoya\ProxySwitcher;

use Zoya\Coin\CoinInterface;
use Zoya\Coin\Never;
use Zoya\InfiniteList;
use Zoya\TorProxyServer;

/**
 * Class Tor
 * @see https://www.torproject.org/docs/tor-manual.html.en
 * @see https://gitweb.torproject.org/torspec.git?a=blob_plain;hb=HEAD;f=control-spec.txt control protocol
 * @package Zoya\ProxySwitcher
 */
class Tor extends Generic
{
    /**
     * Connected
     *
     * @var boolean
     */
    protected $connected = false;

    /**
     * Options
     *
     * @var array
     */
    protected $options;

    /**
     * Socket to the TOR server
     *
     * @var resource
     */
    protected $socket;

    /**
     * @var CoinInterface
     */
    private $coin;

    public function __construct(InfiniteList $proxies, CoinInterface $coin = null)
    {
        if (null === $coin) {
            $coin = new Never();
        }
        $this->coin = $coin;
        parent::__construct($proxies);
    }

    public function switchProxy()
    {
        parent::switchProxy();
        $this->getCoin()->flip();
        if ($this->getCoin()->isLucky()) {
            $this->switchIdentity();
        }
    }

    /**
     * @return \Zoya\Coin\CoinInterface
     */
    public function getCoin()
    {
        return $this->coin;
    }

    public function switchIdentity()
    {
        $this->connect();
        $this->authenticate();
        $this->executeCommand('SIGNAL NEWNYM');
        $this->quit();
    }

    private function checkConnected()
    {
        if (!$this->connected || !$this->socket) {
            throw new \RuntimeException('Not connected');
        }
    }

    private function detectAuthMethod()
    {
        $data = $this->executeCommand('PROTOCOLINFO');

        foreach ($data as $info) {
            if ('AUTH METHODS=NULL' === $info['message']) {
                $this->options['authmethod'] = TorProxyServer::AUTH_METHOD_NULL;

                return;
            }

            if ('AUTH METHODS=HASHEDPASSWORD' === $info['message']) {
                $this->options['authmethod'] = TorProxyServer::AUTH_METHOD_HASHEDPASSWORD;

                return;
            }

            if (preg_match('/^AUTH METHODS=(.*) COOKIEFILE="(.*)"/', $info['message'], $matches) === 1) {
                $this->options['authmethod'] = TorProxyServer::AUTH_METHOD_COOKIE;
                $this->options['cookiefile'] = $matches[2];

                return;
            }
        }

        throw new \RuntimeException('Auth method not supported');
    }

    /**
     * Gets the controller connection status
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * Gets an option
     *
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : false;
    }

    /**
     * Connects to the Tor server
     */
    public function connect()
    {
        if ($this->connected) {
            return;
        }

        $this->socket = @fsockopen($this->getProxy()->getHost(), $this->getProxy()->getControlPort(),
            $errno, $errstr, $this->getProxy()->getTimeout());
        if (!$this->socket) {
            throw new \RuntimeException("Can't connect to the control port. " . $errstr);
        }

        $this->connected = true;

        return $this;
    }

    /**
     * Authenticates to the Tor server
     *
     * Autodetect authentication method if not set in options
     *
     */
    public function authenticate()
    {
        if ($this->getProxy()->getAuthMethod() === TorProxyServer::AUTH_METHOD_NOT_SET) {
            $this->detectAuthMethod();
        }

        switch ($this->options['authmethod']) {
            case TorProxyServer::AUTH_METHOD_NULL:
                $this->executeCommand('AUTHENTICATE');
                break;

            case TorProxyServer::AUTH_METHOD_HASHEDPASSWORD:
                $password = $this->getProxy()->getHashedPassword();
                if ($password === false) {
                    throw new \Exception('You must set a password option');
                }

                $this->executeCommand('AUTHENTICATE ' . static::quote($password));
                break;

            case TorProxyServer::AUTH_METHOD_COOKIE:
                $cookie = file_get_contents($this->options['cookiefile']);

                $this->executeCommand('AUTHENTICATE ' . bin2hex($cookie));
                break;
        }

        return $this;
    }

    /**
     * Executes a command on the Tor server
     *
     * @param string $cmd
     * @throws \RuntimeException
     * @return array
     */
    public function executeCommand($cmd)
    {
        $this->checkConnected();

        $write = @fwrite($this->socket, "$cmd\r\n");
        if ($write === false) {
            throw new \RuntimeException('Error while writing to the Tor server');
        }

        $data = array();
        while (true) {
            $response = fread($this->socket, 1024);
            if (empty($response)) {
                throw new \RuntimeException('Tor authentication failed. Possibly wrong control port
            or tor service is not running');
            }
            foreach (explode("\r\n", $response) as $line) {
                $code = substr($line, 0, 3);
                $separator = substr($line, 3, 1);
                $message = substr($line, 4);

                if ($code === false || $separator === false) {
                    throw new \RuntimeException('Bad response format: ' . $response);
                }

                if (!in_array($separator, array(' ', '+', '-'))) {
                    throw new \RuntimeException('Unknow separator: ' .$response);
                }

                if (!in_array(substr($code, 0, 1), array('2', '6'))) {
                    throw new \RuntimeException("TOR Error. $message", $code);
                }

                $data[] = array(
                    'code' => $code,
                    'separator' => $separator,
                    'message' => $message
                );

                if ($separator === ' ') {
                    break 2;
                }
            }
        }

        return $data;
    }

    /**
     * Closes the connection to the Tor server
     */
    public function quit()
    {
        if ($this->connected && $this->socket) {
            $this->executeCommand("QUIT");
            $close = @fclose($this->socket);
            if (!$close) {
                throw new \RuntimeException('Error while closing the connection to the Tor server');
            }
        }

        $this->connected = false;
    }

    /**
     * Quotes and escapes to use in a command
     *
     * @param string $str
     * @return string
     */
    public static function quote($str)
    {
        $str = strtr($str, array('\\' => '\\\\', '"' => '\"'));

        return '"' . $str . '"';
    }


}

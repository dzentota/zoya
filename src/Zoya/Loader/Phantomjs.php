<?php

namespace Zoya\Loader;

use Valera\Loader\LoaderInterface;
use Valera\Loader\Result as LoaderResult;
use Valera\Loader;
use Valera\Resource;
use Valera\Source;

/**
 * Class Phantomjs
 * @package Zoya\Loader
 */
class Phantomjs implements LoaderInterface
{

    /**
     * @var string
     */
    protected $phantomJS = '/usr/bin/phantomjs';
    /**
     * Overrides default values of Phantomjs settings object
     * @url http://phantomjs.org/api/webpage/property/settings.html
     * @var array
     */
    protected $settings = [];

    /**
     * @var string
     */
    protected $js = <<<EOL

    var page = require('webpage').create(),
    response = {},
    headers = %1\$s;
    %2\$s;
    page.onResourceTimeout = function (e) {
        response = e;
        response.status = e.errorCode;
    };

    page.onResourceReceived = function (r) {
        if(!response.status) response = r;
    };
    page.customHeaders = headers ? headers : {};

    page.open('%3\$s', '%4\$s', '%5\$s', function (status) {
    if (status === 'success') {
        response.content = page.evaluate(function () {
            return document.getElementsByTagName('html')[0].innerHTML
        });
        console.log(JSON.stringify(response, undefined, 4));
        phantom.exit();
    } else {
        console.log(JSON.stringify(response, undefined, 4));
        phantom.exit();
    }
    });

EOL;


    /**
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        //Don't load images
        $defaultSettings = ['loadImages' => false];
        $this->settings = array_merge($defaultSettings, $settings);
    }

    /**
     * @param Source $source
     * @param LoaderResult $result
     */
    public function load(Source $source, LoaderResult $result)
    {
        $response = $this->sendRequest($source->getResource());
        $this->processResponse($response, $result);
    }


    /**
     * @return string
     */
    protected function buildSettings()
    {
        $settings = json_encode($this->settings);
        $js = "var settings = $settings;\n";
        $js .= "for (var i in settings) { page.settings[i] = settings[i];}\n";
        return $js;
    }

    /**
     * @param Resource|\Valera\Resource $resource
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return string
     */
    protected function sendRequest(Resource $resource)
    {
        if (!file_exists($this->phantomJS) || !is_executable($this->phantomJS)) {
            throw new \InvalidArgumentException(
                sprintf('PhantomJs file does not exist or is not executable: %s',
                $this->phantomJS));
        }
        try {

            $script = false;

            $data = sprintf(
                $this->js,
                json_encode($resource->getHeaders()),
                $this->buildSettings(),
                $resource->getUrl(),
                $resource->getMethod(),
                $resource->getData()
            );
            $script = $this->writeScript($data);
            $cmd = escapeshellcmd(sprintf("%s %s", $this->phantomJS, $script));

            $response = shell_exec($cmd);

            $this->removeScript($script);
        } catch (\Exception $e) {
            $this->removeScript($script);
            throw new \Exception(sprintf('Error when executing PhantomJs command: %s - %s',
                $cmd, $e->getMessage()));
        }

        return $response;
    }

    /**
     * @param $data
     * @return string
     * @throws \Exception
     */
    protected function writeScript($data)
    {
        $file = tempnam('/tmp', 'phantomjs');

        // Could not create tmp file
        if (!$file || !is_writable($file)) {
            throw new \Exception(
                'Could not create tmp file on system. Please check your tmp directory and make sure it is writeable.');
        }
        // Could not write script data to tmp file
        if (file_put_contents($file, $data) === false) {
            $this->removeScript($file);
            throw new \Exception(
                sprintf('Could not write data to tmp file: %s.
                Please check your tmp directory and make sure it is writeable.', $file));
        }
        return $file;
    }

    /**
     * @param $file
     * @return $this
     */
    protected function removeScript($file)
    {
        if (is_string($file) && file_exists($file)) {
            unlink($file);
        }
        return $this;
    }


    /**
     * @param $response
     * @param LoaderResult $result
     */
    protected function processResponse( $response, LoaderResult $result)
    {
        if ($response === null || !is_string($response)) {
            $result->fail('Unexpected response');
        }

        // Not a JSON string
        if (substr($response, 0, 1) !== '{') {
            $result->fail('Bad response format');
            return;
        }
        $data = json_decode($response, true);
        //Maybe content can be blank on POST request?
        if (empty($data['status']) || empty($data['content'])) {
            $result->fail('No content found');
            return;
        }
        if ($data['status'] > 400 && $data['status'] < 600) {
            $result->fail("HTTP code: " . $data['status']);
        } else {
            $result->setContent($data['content']);
        }
    }

}

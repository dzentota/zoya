<?php

namespace Zoya\Loader;

use Assert\Assertion;
use Valera\Loader\LoaderInterface;
use Valera\Loader\Result as LoaderResult;
use Valera\Loader;
use Valera\Resource;

/**
 * Class Phantomjs
 * @package Zoya\Loader
 */
class Phantomjs implements LoaderInterface
{

    /**
     * @var string
     */
    protected $phantomJS;
    /**
     * Overrides default values of Phantomjs settings object
     * @url http://phantomjs.org/api/webpage/property/settings.html
     * @var array
     */
    protected $settings = ['loadImages' => false];

    protected $cliOptions = [];
    /**
     * @param array $settings
     * @param string $phantomJS
     */
    public function __construct(array $settings = [], $phantomJS = '/usr/bin/phantomjs')
    {
        $this->phantomJS = $phantomJS;
        //Don't load images
        $this->addSettings($settings);
    }

    /**
     * @return string
     */
    public function getPhantomJS()
    {
        return $this->phantomJS;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    public function getCliOptions()
    {
        return $this->cliOptions;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setCliOptions(array $options = [])
    {
        $this->cliOptions = $options;
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function addCliOptions(array $options = [])
    {
        $this->cliOptions = array_merge($this->cliOptions, $options);
        return $this;
    }

    protected function buildCliOptions()
    {
        $cliOptions = [];
        if ($options = $this->getCliOptions()) {
            foreach ($options as $k=>$v) {

                $cliOptions[] = sprintf("--%s=%s", $k, escapeshellarg($v));
            }
        }
        return implode(' ',  $cliOptions);
    }

    /**
     * @param $path
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setPhantomJS($path)
    {
        $this->phantomJS = $path;
        return $this;
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function setSettings(array $settings = [])
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function addSettings(array $settings = [])
    {
        $this->settings = array_merge($this->settings, $settings);
        return $this;
    }

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    public function addSetting($key, $val)
    {
        $this->settings[$key] = $val;
        return $this;
    }

    /**
     * @param \Valera\Resource $source
     * @param LoaderResult $result
     */
    public function load(Resource $source, LoaderResult $result)
    {
        $response = $this->sendRequest($source);
        $this->processResponse($response, $result);
    }

    /**
     * @param Resource|\Valera\Resource $resource
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return string
     */
    protected function sendRequest(Resource $resource)
    {
        $phantomJS = $this->getPhantomJS();
        if (!file_exists($phantomJS) || !is_executable($phantomJS)) {
            throw new \InvalidArgumentException(
                sprintf('PhantomJs file does not exist or is not executable: %s',
                $phantomJS));
        }
        try {
            $cmd = escapeshellcmd(
                sprintf("%s %s %s %s %s %s %s %s",
                        $phantomJS,
                        $this->buildCliOptions(),
                        __DIR__ . '/loader.js',
                        json_encode($resource->getHeaders()),
                        json_encode($this->settings),
                        $resource->getUrl(),
                        $resource->getMethod(),
                        $resource->getPayload()
                    )
                );
            $response = shell_exec($cmd);

        } catch (\Exception $e) {

            throw new \Exception(sprintf('Error when executing PhantomJs command: %s - %s',
                $cmd, $e->getMessage()));
        }
        return $response;
    }


    /**
     * @param $response
     * @param LoaderResult $result
     */
    protected function processResponse( $response, LoaderResult $result)
    {
        if ($response === null || !is_string($response)) {
            $result->fail('Unexpected response');
            return;
        }

        $data = json_decode($response, true);
        if (is_null($data)) {
            $result->fail('Bad response format');
            return;
        }
        //Maybe content can be blank on POST request?
        if (empty($data['status']) || empty($data['content'])) {
            $result->fail('No content found');
            return;
        }
        if ($data['status'] >= 400 && $data['status'] < 600) {
            $result->fail("HTTP code: " . $data['status']);
        } else {
            list($contentType, $charset) = explode(';', $data['contentType']);
            $content = "<html>{$data['content']}</html>";
            $result->setContent($content, strval($contentType));
        }

    }

}

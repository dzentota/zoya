<?php

namespace Zoya\Tests;

use Zoya\Loader\Phantomjs;

/**
 * @covers \Zoya\Phantomjs
 */
class PhantomjsTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaults()
    {
        $phantom = new Phantomjs();
        $this->assertEquals($phantom->getPhantomJS(), '/usr/bin/phantomjs');
        $this->assertEquals($phantom->getSettings(), ['loadImages' => false]);
    }

    public function testSetGet()
    {
        $phantom = new Phantomjs();

        $file = tempnam(sys_get_temp_dir(), 'phantom');

        $phantom->setPhantomJS($file);
        $this->assertEquals($phantom->getPhantomJS(), $file);

        $settings = ['foo'=>'bar'];
        $phantom->setSettings($settings);
        $this->assertEquals($phantom->getSettings(), $settings);

    }

    public function testAddSetting()
    {
        $phantom = new Phantomjs();

        $phantom->addSetting('key','val');
        $this->assertArrayHasKey('key', $phantom->getSettings());
        $this->assertContains('val', $phantom->getSettings());
    }

    public function testAddSettings()
    {
        $phantom = new Phantomjs();

        $phantom->addSettings(['key'=>'val']);
        $this->assertArrayHasKey('key', $phantom->getSettings());
        $this->assertContains('val', $phantom->getSettings());

        //check that defaults persist
        $this->assertEquals(['loadImages' => false, 'key'=>'val'], $phantom->getSettings());
    }


    public function testSuccessResponse()
    {
        $response = $this->getResponse();
        $result = $this->getResultMock('setContent');
        $this->callProcessResponse($response, $result);
    }


    /**
     * @dataProvider failResponseProvider
     */
    public function testFailedResponse($response)
    {
        $result = $this->getResultMock('fail', false);
        $this->callProcessResponse($response, $result);
    }

    public function failResponseProvider()
    {
        return [
            [ null ],
            [ [] ],
            [ 123 ],
            [ '{ "broken" : json' ],
            //Empty status
            [ ['status'=> null, 'content'=>'some content'] ],
            //Empty content
            [ ['status'=>200 , 'content'=> null] ],
            //Bad HTTP code
            [ ['status'=> 500] ]
        ];
    }

    private function getResultMock($expectedMethod, $once = true)
    {
        $result = $this->getMockBuilder('Valera\\Loader\\Result')
            ->disableOriginalConstructor()
            ->getMock();
        $result->expects($once? $this->once() : $this->any())
            ->method($expectedMethod);

        return $result;
    }

    private function getResponse()
    {
        $response = [
            'bodySize' => 256,
            'contentType' => 'text/html; charset=UTF-8',
            'headers' => [
                [
                    'name' => 'Location',
                    'value'=> 'http://www.google.by/'
                ],
                [
                    'name' => 'Cache-Control',
                    'value' => 'private'
                ]
            ],
            'status' => 200,
            'url' => 'http://google.com',
            'content' => '<p>Content goes here</p>'
        ];

        return json_encode($response);
    }

    private function callProcessResponse($response, $result)
    {
        $loader = $this->getMockBuilder('Zoya\\Loader\\Phantomjs')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $re = new \ReflectionMethod($loader, 'processResponse');
        $re->setAccessible(true);
        $re->invoke($loader, $response, $result);
    }
}

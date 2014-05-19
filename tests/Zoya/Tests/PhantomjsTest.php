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


}

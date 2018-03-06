<?php

namespace Tests\ShopBundle\Unit\Component\Setting;

use DateTime;
use PHPUnit_Framework_TestCase;
use Shopsys\FrameworkBundle\Component\Setting\Exception\InvalidArgumentException;
use Shopsys\FrameworkBundle\Component\Setting\SettingValue;
use stdClass;

class SettingValueTest extends PHPUnit_Framework_TestCase
{
    public function editProvider()
    {
        return [
            ['string'],
            [0],
            [0.0],
            [false],
            [true],
            [null],
        ];
    }

    public function editExceptionProvider()
    {
        return [
            [[]],
            [new stdClass()],
        ];
    }

    /**
     * @dataProvider editProvider
     */
    public function testEdit($value)
    {
        $settingValue = new SettingValue('name', $value, 1);
        $this->assertSame($value, $settingValue->getValue());
    }

    /**
     * @dataProvider editExceptionProvider
     */
    public function testEditException($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new SettingValue('name', $value, 1);
    }

    public function testStoreDatetime()
    {
        $value = new DateTime('2017-01-01 12:34:56');
        $settingValue = new SettingValue('name', $value, 1);
        $this->assertEquals($value, $settingValue->getValue());
    }
}

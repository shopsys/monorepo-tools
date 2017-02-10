<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Utils;

class UtilsTest extends PHPUnit_Framework_TestCase
{

    public function testIfNull() {
        $this->assertTrue(Utils::ifNull(null, true));
        $this->assertFalse(Utils::ifNull(false, true));
        $this->assertTrue(Utils::ifNull(true, false));
    }

    public function testSetArrayDefaultValueExists() {
        $array = [
            'key' => 'value',
        ];
        $expectedArray = $array;
        Utils::setArrayDefaultValue($array, 'key', 'defaultValue');

        $this->assertSame($expectedArray, $array);
    }

    public function testSetArrayDefaultValueExistsNull() {
        $array = [
            'key' => null,
        ];
        $expectedArray = $array;
        Utils::setArrayDefaultValue($array, 'key', 'defaultValue');

        $this->assertSame($expectedArray, $array);
    }

    public function testSetArrayDefaultValueNotExist() {
        $array = [
            'key' => null,
        ];
        $expectedArray = [
            'key' => null,
            0 => 'number',
        ];
        Utils::setArrayDefaultValue($array, 0, 'number');

        $this->assertSame($expectedArray, $array);
    }

    public function testMixedToArrayIfNull() {
        $this->assertSame([], Utils::mixedToArray(null));
    }

    public function testMixedToArrayIfNotArray() {
        $value = 'I am not array';
        $this->assertSame([$value], Utils::mixedToArray($value));
    }

    public function testMixedToArrayIfArray() {
        $value = ['1', 3, []];
        $this->assertSame($value, Utils::mixedToArray($value));
    }
}

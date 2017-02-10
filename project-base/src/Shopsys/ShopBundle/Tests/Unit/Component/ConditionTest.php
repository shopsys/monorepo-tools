<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Condition;

class ConditionTest extends PHPUnit_Framework_TestCase {

	public function testIfNull() {
		$this->assertTrue(Condition::ifNull(null, true));
		$this->assertFalse(Condition::ifNull(false, true));
		$this->assertTrue(Condition::ifNull(true, false));
	}

	public function testSetArrayDefaultValueExists() {
		$array = [
			'key' => 'value',
		];
		$expectedArray = $array;
		Condition::setArrayDefaultValue($array, 'key', 'defaultValue');

		$this->assertSame($expectedArray, $array);
	}

	public function testSetArrayDefaultValueExistsNull() {
		$array = [
			'key' => null,
		];
		$expectedArray = $array;
		Condition::setArrayDefaultValue($array, 'key', 'defaultValue');

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
		Condition::setArrayDefaultValue($array, 0, 'number');

		$this->assertSame($expectedArray, $array);
	}

	public function testMixedToArrayIfNull() {
		$this->assertSame([], Condition::mixedToArray(null));
	}

	public function testMixedToArrayIfNotArray() {
		$value = 'I am not array';
		$this->assertSame([$value], Condition::mixedToArray($value));
	}

	public function testMixedToArrayIfArray() {
		$value = ['1', 3, []];
		$this->assertSame($value, Condition::mixedToArray($value));
	}
}

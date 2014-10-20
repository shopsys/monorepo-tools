<?php

namespace SS6\ShopBundle\Tests\Component;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Condition;

class ConditionTest extends PHPUnit_Framework_TestCase {

	public function testIfNull() {
		$this->assertTrue(Condition::ifNull(null, true));
		$this->assertFalse(Condition::ifNull(false, true));
		$this->assertTrue(Condition::ifNull(true, false));
	}

	public function testSetArrayDefaultValueExists() {
		$array = array(
			'key' => 'value',
		);
		$expectedArray = $array;
		Condition::setArrayDefaultValue($array, 'key', 'defaultValue');

		$this->assertEquals($expectedArray, $array);
	}

	public function testSetArrayDefaultValueExistsNull() {
		$array = array(
			'key' => null,
		);
		$expectedArray = $array;
		Condition::setArrayDefaultValue($array, 'key', 'defaultValue');

		$this->assertEquals($expectedArray, $array);
	}

	public function testSetArrayDefaultValueNotExist() {
		$array = array(
			'key' => null,
		);
		$expectedArray = array(
			'key' => null,
			0 => 'number',
		);
		Condition::setArrayDefaultValue($array, 0, 'number');

		$this->assertEquals($expectedArray, $array);
	}

	public function testMixedToArrayIfNull() {
		$this->assertEquals([], Condition::mixedToArray(null));
	}

	public function testMixedToArrayIfNotArray() {
		$value = 'I am not array';
		$this->assertEquals([$value], Condition::mixedToArray($value));
	}

	public function testMixedToArrayIfArray() {
		$value = ['1', 3, []];
		$this->assertEquals($value, Condition::mixedToArray($value));
	}
}

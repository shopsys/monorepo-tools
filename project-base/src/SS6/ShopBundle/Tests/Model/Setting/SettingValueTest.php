<?php

namespace SS6\ShopBundle\Tests\Setting;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Setting\Exception\InvalidArgumentException;
use SS6\ShopBundle\Model\Setting\SettingValue;
use stdClass;

class SettingValueTest extends PHPUnit_Framework_TestCase {

	public function testEditProvider() {
		return array(
			['string'],
			[0],
			[0.0],
			[false],
			[true],
			[null],
		);
	}

	public function testEditExceptionProvider() {
		return array(
			[[]],
			[new stdClass()],
		);
	}

	/**
	 * @dataProvider testEditProvider
	 */
	public function testEdit($value) {
		$settingValue = new SettingValue('name', $value, 1);
		$this->assertEquals($value, $settingValue->getValue());
	}

	/**
	 * @dataProvider testEditExceptionProvider
	 */
	public function testEditException($value) {
		$this->setExpectedException(InvalidArgumentException::class);
		new SettingValue('name', $value, 1);
	}

}

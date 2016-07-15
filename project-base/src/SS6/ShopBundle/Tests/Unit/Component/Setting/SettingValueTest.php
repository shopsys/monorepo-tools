<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Setting;

use DateTime;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Setting\Exception\InvalidArgumentException;
use SS6\ShopBundle\Component\Setting\SettingValue;
use stdClass;

/**
 * @UglyTest
 */
class SettingValueTest extends PHPUnit_Framework_TestCase {

	public function editProvider() {
		return [
			['string'],
			[0],
			[0.0],
			[false],
			[true],
			[null],
		];
	}

	public function editExceptionProvider() {
		return [
			[[]],
			[new stdClass()],
		];
	}

	/**
	 * @dataProvider editProvider
	 */
	public function testEdit($value) {
		$settingValue = new SettingValue('name', $value, 1);
		$this->assertSame($value, $settingValue->getValue());
	}

	/**
	 * @dataProvider editExceptionProvider
	 */
	public function testEditException($value) {
		$this->setExpectedException(InvalidArgumentException::class);
		new SettingValue('name', $value, 1);
	}

	public function testStoreDatetime() {
		$value = new DateTime();
		$settingValue = new SettingValue('name', $value, 1);
		$this->assertEquals($value, $settingValue->getValue());
	}

}

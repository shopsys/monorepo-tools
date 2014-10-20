<?php

namespace SS6\ShopBundle\Tests\Model\Form;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Form\MultipleFormSetting;

class MultipleFormSettingTest extends PHPUnit_Framework_TestCase {

	public function testCurrentFormIsMultiple() {
		$multipleFormSetting = new MultipleFormSetting();
		$multipleFormSetting->currentFormIsMultiple();

		$this->assertTrue($multipleFormSetting->isCurrentFormMultiple());
	}

	public function testCurrentFormIsNotMultiple() {
		$multipleFormSetting = new MultipleFormSetting();
		$multipleFormSetting->currentFormIsNotMultiple();

		$this->assertFalse($multipleFormSetting->isCurrentFormMultiple());
	}

	public function testDefaultValue() {
		$multipleFormSetting = new MultipleFormSetting();

		$this->assertEquals(MultipleFormSetting::DEFAULT_MULTIPLE, $multipleFormSetting->isCurrentFormMultiple());
	}

	public function testReset() {
		$multipleFormSetting = new MultipleFormSetting();

		$multipleFormSetting->reset();
		$this->assertEquals(MultipleFormSetting::DEFAULT_MULTIPLE, $multipleFormSetting->isCurrentFormMultiple());

		$multipleFormSetting->currentFormIsMultiple();
		$multipleFormSetting->reset();
		$this->assertEquals(MultipleFormSetting::DEFAULT_MULTIPLE, $multipleFormSetting->isCurrentFormMultiple());

		$multipleFormSetting->currentFormIsNotMultiple();
		$multipleFormSetting->reset();
		$this->assertEquals(MultipleFormSetting::DEFAULT_MULTIPLE, $multipleFormSetting->isCurrentFormMultiple());
	}
}

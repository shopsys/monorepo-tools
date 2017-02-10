<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Form;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Form\MultipleFormSetting;

class MultipleFormSettingTest extends PHPUnit_Framework_TestCase
{
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

        $this->assertSame(MultipleFormSetting::DEFAULT_MULTIPLE, $multipleFormSetting->isCurrentFormMultiple());
    }

    public function testReset() {
        $multipleFormSetting = new MultipleFormSetting();

        $multipleFormSetting->reset();
        $this->assertSame(MultipleFormSetting::DEFAULT_MULTIPLE, $multipleFormSetting->isCurrentFormMultiple());

        $multipleFormSetting->currentFormIsMultiple();
        $multipleFormSetting->reset();
        $this->assertSame(MultipleFormSetting::DEFAULT_MULTIPLE, $multipleFormSetting->isCurrentFormMultiple());

        $multipleFormSetting->currentFormIsNotMultiple();
        $multipleFormSetting->reset();
        $this->assertSame(MultipleFormSetting::DEFAULT_MULTIPLE, $multipleFormSetting->isCurrentFormMultiple());
    }
}

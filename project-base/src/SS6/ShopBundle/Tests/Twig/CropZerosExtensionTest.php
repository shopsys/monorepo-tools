<?php

namespace SS6\ShopBundle\Tests\Twig;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Twig\CropZerosExtension;

class CropZerosExtensionTest extends PHPUnit_Framework_TestCase {

	public function returnValuesProvider() {
		return array(
			array('input' => '12', 'return' => '12'),
			array('input' => '12.00', 'return' => '12'),
			array('input' => '12,00', 'return' => '12'),
			array('input' => '12.630000', 'return' => '12.63'),
			array('input' => '12,630000', 'return' => '12,63'),
			array('input' => '1200', 'return' => '1200'),
		);
	}

	/**
	 * @dataProvider returnValuesProvider
	 */
	public function testReturnValues($input, $return) {
		$this->assertEquals($return, (new CropZerosExtension())->cropZeros($input));
	}
}

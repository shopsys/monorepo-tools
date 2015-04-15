<?php

namespace SS6\ShopBundle\Tests\Unit\Twig;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Twig\CropZerosExtension;

class CropZerosExtensionTest extends PHPUnit_Framework_TestCase {

	public function returnValuesProvider() {
		return [
			['input' => '12', 'return' => '12'],
			['input' => '12.00', 'return' => '12'],
			['input' => '12,00', 'return' => '12'],
			['input' => '12.630000', 'return' => '12.63'],
			['input' => '12,630000', 'return' => '12,63'],
			['input' => '1200', 'return' => '1200'],
		];
	}

	/**
	 * @dataProvider returnValuesProvider
	 */
	public function testReturnValues($input, $return) {
		$this->assertSame($return, (new CropZerosExtension())->cropZeros($input));
	}
}

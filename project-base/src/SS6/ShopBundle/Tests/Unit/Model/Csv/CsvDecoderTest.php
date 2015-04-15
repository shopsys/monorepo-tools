<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Customer;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Csv\CsvDecoder;

class CsvDecoderTest extends PHPUnit_Framework_TestCase {

	public function getTestDecodeBooleanData() {
		return [
			['input' => 'true', 'output' => true],
			['input' => 'false', 'output' => false],
			['input' => 'asdf', 'output' => false],
			['input' => '', 'output' => false],
		];
	}

	/**
	 * @dataProvider getTestDecodeBooleanData
	 */
	public function testDecodeBoolean($input, $output) {
		$boolean = CsvDecoder::decodeBoolean($input);

		$this->assertSame($output, $boolean);
	}

}

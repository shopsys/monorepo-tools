<?php

namespace SS6\ShopBundle\Tests\Model\Customer;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Csv\CsvDecoder;

class CsvDecoderTest extends PHPUnit_Framework_TestCase {

	public function getTestDecodeBooleanData() {
		return array(
			array('input' => 'true', 'output' => true),
			array('input' => 'false', 'output' => false),
			array('input' => 'asdf', 'output' => false),
			array('input' => '', 'output' => false),
		);
	}

	/**
	 * @dataProvider getTestDecodeBooleanData
	 */
	public function testDecodeBoolean($input, $output) {
		$boolean = CsvDecoder::decodeBoolean($input);

		$this->assertEquals($output, $boolean);
	}

}

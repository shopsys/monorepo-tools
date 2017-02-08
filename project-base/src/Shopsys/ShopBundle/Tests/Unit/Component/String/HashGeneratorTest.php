<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\String;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\String\HashGenerator;

class HashGeneratorTest extends PHPUnit_Framework_TestCase {

	public function hashLengthProvider() {
		return [
			[1],
			[13],
			[100],
		];
	}

	/**
	 * @dataProvider hashLengthProvider
	 */
	public function testGenerateHash($length) {
		$hashGererator = new HashGenerator();

		$hash = $hashGererator->generateHash($length);

		$this->assertSame($length, strlen($hash));
	}

}

<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Fulltext;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Fulltext\TsqueryFactory;

class TsqueryFactoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getTsqueryWithAndConditionsData
	 */
	public function testGetTsqueryWithAndConditions($searchText, $expectedResult) {
		$tsqueryFactory = new TsqueryFactory();

		$result = $tsqueryFactory->getTsqueryWithAndConditions($searchText);

		$this->assertSame($expectedResult, $result);
	}

	public function getTsqueryWithAndConditionsData() {
		return [
			[null, ''],
			['', ''],
			['asdf', 'asdf'],
			['one two', 'one & two'],
			["one  \t\n two", 'one & two'],
			['at&t', 'at & t'],
			['full-text', 'full-text'],
		];
	}

}

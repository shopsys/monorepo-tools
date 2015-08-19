<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Fulltext;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Fulltext\TsqueryFactory;

class TsqueryFactoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getSplitToTokensData
	 */
	public function testSplitToTokens($searchText, $expectedResult) {
		$tsqueryFactory = new TsqueryFactory();

		$result = $tsqueryFactory->splitToTokens($searchText);

		$this->assertSame($expectedResult, $result);
	}

	public function getSplitToTokensData() {
		return [
			[null, []],
			['', []],
			['asdf', ['asdf']],
			['one two', ['one', 'two']],
			["one  \t\n two", ['one', 'two']],
			['at&t', ['at', 't']],
			['full-text', ['full-text']],
			['přílišžluťoučkýkůňúpělďábelskéódy', ['přílišžluťoučkýkůňúpělďábelskéódy']],
			['PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY', ['PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY']],
		];
	}

	public function testGetTsqueryWithAndConditions() {
		$tsqueryFactory = new TsqueryFactory();

		$result = $tsqueryFactory->getTsqueryWithAndConditions('one two three');

		$this->assertSame('one & two & three', $result);
	}

	public function testGetTsqueryWithOrConditions() {
		$tsqueryFactory = new TsqueryFactory();

		$result = $tsqueryFactory->getTsqueryWithOrConditions('one two three');

		$this->assertSame('one | two | three', $result);
	}

}

<?php

namespace SS6\ShopBundle\Tests\Model\String;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\String\DatabaseSearching;

class DatabaseSearchingTest extends PHPUnit_Framework_TestCase {

	public function searchTextProvider() {
		return array(
			array('searchText' => 'foo bar', 'querySearchStringQuery' => 'foo bar'),
			array('searchText' => 'FooBar', 'querySearchStringQuery' => 'FooBar'),
			array('searchText' => 'foo*bar', 'querySearchStringQuery' => 'foo%bar'),
			array('searchText' => 'foo%', 'querySearchStringQuery' => 'foo\%'),
			array('searchText' => 'fo?o%', 'querySearchStringQuery' => 'fo_o\%'),
			array('searchText' => '_foo', 'querySearchStringQuery' => '\_foo'),
		);
	}

	/**
	 * @dataProvider searchTextProvider
	 */
	public function testSafeFilename($searchText, $querySearchStringQuery) {
		$this->assertEquals($querySearchStringQuery, DatabaseSearching::getLikeSearchString($searchText));
	}
	
}

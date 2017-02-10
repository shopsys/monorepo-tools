<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\String;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\String\DatabaseSearching;

class DatabaseSearchingTest extends PHPUnit_Framework_TestCase {

    public function searchTextProvider() {
        return [
            ['searchText' => 'foo bar', 'querySearchStringQuery' => 'foo bar'],
            ['searchText' => 'FooBar', 'querySearchStringQuery' => 'FooBar'],
            ['searchText' => 'foo*bar', 'querySearchStringQuery' => 'foo%bar'],
            ['searchText' => 'foo%', 'querySearchStringQuery' => 'foo\%'],
            ['searchText' => 'fo?o%', 'querySearchStringQuery' => 'fo_o\%'],
            ['searchText' => '_foo', 'querySearchStringQuery' => '\_foo'],
        ];
    }

    /**
     * @dataProvider searchTextProvider
     */
    public function testSafeFilename($searchText, $querySearchStringQuery) {
        $this->assertSame($querySearchStringQuery, DatabaseSearching::getLikeSearchString($searchText));
    }

}

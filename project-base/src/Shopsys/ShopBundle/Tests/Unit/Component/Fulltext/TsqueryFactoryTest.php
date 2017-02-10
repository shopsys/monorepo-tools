<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Fulltext;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Fulltext\TsqueryFactory;

class TsqueryFactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getIsValidSearchTextData
     */
    public function testIsValidSearchText($searchText, $expectedResult) {
        $tsqueryFactory = new TsqueryFactory();

        $result = $tsqueryFactory->isValidSearchText($searchText);

        $this->assertSame($expectedResult, $result);
    }

    public function getIsValidSearchTextData() {
        return [
            [null, false],
            ['', false],
            ['asdf', true],
            ['one two', true],
            ["one  \t\n two", true],
            ['at&t', true],
            ['full-text', true],
            ['přílišžluťoučkýkůňúpělďábelskéódy', true],
            ['PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY', true],
            ['ab*', true],
            ['ab:*', true],
            ['a*:b', true],
            ['a:* b', true],
        ];
    }

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
            ['přílišžluťoučkýkůňúpělďábelskéódy', 'přílišžluťoučkýkůňúpělďábelskéódy'],
            ['PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY', 'PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY'],
            ['ab*', 'ab'],
            ['ab:*', 'ab'],
            ['a*:b', 'a & b'],
            ['a:* b', 'a & b'],
        ];
    }

    /**
     * @dataProvider getTsqueryWithAndConditionsAndPrefixMatchForLastWordData
     */
    public function testGetTsqueryWithAndConditionsAndPrefixMatchForLastWord($searchText, $expectedResult) {
        $tsqueryFactory = new TsqueryFactory();

        $result = $tsqueryFactory->getTsqueryWithAndConditionsAndPrefixMatchForLastWord($searchText);

        $this->assertSame($expectedResult, $result);
    }

    public function getTsqueryWithAndConditionsAndPrefixMatchForLastWordData() {
        return [
            [null, ''],
            ['', ''],
            ['asdf', 'asdf:*'],
            ['one two', 'one & two:*'],
            ["one  \t\n two", 'one & two:*'],
            ['at&t', 'at & t:*'],
            ['full-text', 'full-text:*'],
            ['přílišžluťoučkýkůňúpělďábelskéódy', 'přílišžluťoučkýkůňúpělďábelskéódy:*'],
            ['PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY', 'PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY:*'],
            ['ab*', 'ab:*'],
            ['ab:*', 'ab:*'],
            ['a*:b', 'a & b:*'],
            ['a:* b', 'a & b:*'],
        ];
    }

    /**
     * @dataProvider getTsqueryWithOrConditionsData
     */
    public function testGetTsqueryWithOrConditions($searchText, $expectedResult) {
        $tsqueryFactory = new TsqueryFactory();

        $result = $tsqueryFactory->getTsqueryWithOrConditions($searchText);

        $this->assertSame($expectedResult, $result);
    }

    public function getTsqueryWithOrConditionsData() {
        return [
            [null, ''],
            ['', ''],
            ['asdf', 'asdf'],
            ['one two', 'one | two'],
            ["one  \t\n two", 'one | two'],
            ['at&t', 'at | t'],
            ['full-text', 'full-text'],
            ['přílišžluťoučkýkůňúpělďábelskéódy', 'přílišžluťoučkýkůňúpělďábelskéódy'],
            ['PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY', 'PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY'],
            ['ab*', 'ab'],
            ['ab:*', 'ab'],
            ['a*:b', 'a | b'],
            ['a:* b', 'a | b'],
        ];
    }

    /**
     * @dataProvider getTsqueryWithOrConditionsAndPrefixMatchForLastWordData
     */
    public function testGetTsqueryWithOrConditionsAndPrefixMatchForLastWord($searchText, $expectedResult) {
        $tsqueryFactory = new TsqueryFactory();

        $result = $tsqueryFactory->getTsqueryWithOrConditionsAndPrefixMatchForLastWord($searchText);

        $this->assertSame($expectedResult, $result);
    }

    public function getTsqueryWithOrConditionsAndPrefixMatchForLastWordData() {
        return [
            [null, ''],
            ['', ''],
            ['asdf', 'asdf:*'],
            ['one two', 'one | two:*'],
            ["one  \t\n two", 'one | two:*'],
            ['at&t', 'at | t:*'],
            ['full-text', 'full-text:*'],
            ['přílišžluťoučkýkůňúpělďábelskéódy', 'přílišžluťoučkýkůňúpělďábelskéódy:*'],
            ['PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY', 'PŘÍLIŠŽLUŤOUČKÝKŮŇÚPĚLĎÁBELSKÉÓDY:*'],
            ['ab*', 'ab:*'],
            ['ab:*', 'ab:*'],
            ['a*:b', 'a | b:*'],
            ['a:* b', 'a | b:*'],
        ];
    }
}

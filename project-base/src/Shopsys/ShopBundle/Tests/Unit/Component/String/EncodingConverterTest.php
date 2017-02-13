<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\String;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\String\EncodingConverter;
use stdClass;

class EncodingConverterTest extends PHPUnit_Framework_TestCase
{
    const STRING_UTF8 = 'příšerně žluťoučký kůň úpěl ďábelské ódy. PŘÍŠERNĚ ŽLUŤOUČKÝ KŮŇ ÚPĚL ĎÁBELSKÉ ÓDY.';

    private function getUtf8String()
    {
        return self::STRING_UTF8;
    }

    private function getCp1250String()
    {
        return iconv('UTF-8', 'CP1250', self::STRING_UTF8);
    }

    public function testCp1250ToUtf8()
    {
        $this->assertSame($this->getUtf8String(), EncodingConverter::cp1250ToUtf8($this->getCp1250String()));
    }

    public function testCp1250ToUtf8Array()
    {
        $array = ['key' => $this->getUtf8String()];
        $actual = EncodingConverter::cp1250ToUtf8([
            'key' => $this->getCp1250String(),
        ]);
        $this->assertSame($array, $actual);
    }

    public function testCp1250ToUtf8ArrayOfArrays()
    {
        $array = ['key' => $this->getUtf8String()];
        $arrayOfArrays = ['array' => $array];
        $actual = EncodingConverter::cp1250ToUtf8([
            'array' => ['key' => $this->getCp1250String()],
        ]);
        $this->assertSame($arrayOfArrays, $actual);
    }

    public function testCp1250ToUtf8Object()
    {
        $object = new stdClass();
        $actual = EncodingConverter::cp1250ToUtf8($object);
        $this->assertSame($object, $actual);
    }

    public function testCp1250ToUtf8ArrayOfMixed()
    {
        $array = ['key' => $this->getUtf8String()];
        $object = new stdClass();
        $arrayOfMixed = ['string' => $this->getUtf8String(), 'array' => $array, 'object' => $object];
        $actual = EncodingConverter::cp1250ToUtf8([
            'string' => $this->getCp1250String(),
            'array' => ['key' => $this->getCp1250String()],
            'object' => $object,
        ]);
        $this->assertSame($arrayOfMixed, $actual);
    }
}

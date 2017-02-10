<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Router;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Transformers\RemoveWhitespacesTransformer;

class RemoveWhitespacesTransformerTest extends PHPUnit_Framework_TestCase
{

    public function transformValuesProvider() {
        return [
            ['value' => 'foo bar', 'expected' => 'foobar'],
            ['value' => 'FooBar', 'expected' => 'FooBar'],
            ['value' => '  foo  bar  ', 'expected' => 'foobar'],
            ['value' => "foo\t", 'expected' => 'foo'],
            ['value' => "fo\no", 'expected' => 'foo'],
            ['value' => null, 'expected' => null],
        ];
    }

    /**
     * @dataProvider transformValuesProvider
     */
    public function testReverseTransform($value, $expected) {
        $transformer = new RemoveWhitespacesTransformer();
        $this->assertSame($expected, $transformer->reverseTransform($value));
    }

}

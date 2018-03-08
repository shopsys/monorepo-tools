<?php

namespace Tests\ShopBundle\Unit\Component\Transformers;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Transformers\RemoveWhitespacesTransformer;

class RemoveWhitespacesTransformerTest extends TestCase
{
    public function transformValuesProvider()
    {
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
    public function testReverseTransform($value, $expected)
    {
        $transformer = new RemoveWhitespacesTransformer();
        $this->assertSame($expected, $transformer->reverseTransform($value));
    }
}

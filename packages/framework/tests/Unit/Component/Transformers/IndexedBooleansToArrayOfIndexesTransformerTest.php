<?php

namespace Tests\FrameworkBundle\Unit\Component\Transformers;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Form\Transformers\IndexedBooleansToArrayOfIndexesTransformer;

class IndexedBooleansToArrayOfIndexesTransformerTest extends TestCase
{
    public function transformValuesProvider()
    {
        return [
            ['value' => [], 'expected' => []],
            ['value' => [1, 2, 3], 'expected' => [1 => true, 2 => true, 3 => true]],
            ['value' => ['foo'], 'expected' => ['foo' => true]],
            ['value' => 'foo', 'expected' => null],
            ['value' => null, 'expected' => null],
        ];
    }

    /**
     * @dataProvider transformValuesProvider
     */
    public function testTransform($value, $expected)
    {
        $transformer = new IndexedBooleansToArrayOfIndexesTransformer();
        $this->assertSame($expected, $transformer->transform($value));
    }

    public function reverseTransformValuesProvider()
    {
        return [
            ['value' => [], 'expected' => []],
            ['value' => [1 => true, 2 => true, 3 => true], 'expected' => [1, 2, 3]],
            ['value' => [1 => false, 2 => true, 3 => false], 'expected' => [2]],
            ['value' => ['foo' => true], 'expected' => ['foo']],
            ['value' => ['foo' => false], 'expected' => []],
            ['value' => 'foo', 'expected' => null],
            ['value' => null, 'expected' => null],
        ];
    }

    /**
     * @dataProvider reverseTransformValuesProvider
     */
    public function testReverseTransform($value, $expected)
    {
        $transformer = new IndexedBooleansToArrayOfIndexesTransformer();
        $this->assertSame($expected, $transformer->reverseTransform($value));
    }
}

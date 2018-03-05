<?php

namespace Tests\ShopBundle\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Twig\CropZerosExtension;

class CropZerosExtensionTest extends TestCase
{
    public function returnValuesProvider()
    {
        return [
            ['input' => '12', 'return' => '12'],
            ['input' => '12.00', 'return' => '12'],
            ['input' => '12,00', 'return' => '12'],
            ['input' => '12.630000', 'return' => '12.63'],
            ['input' => '12,630000', 'return' => '12,63'],
            ['input' => '1200', 'return' => '1200'],
        ];
    }

    /**
     * @dataProvider returnValuesProvider
     */
    public function testReturnValues($input, $return)
    {
        $this->assertSame($return, (new CropZerosExtension())->cropZeros($input));
    }
}

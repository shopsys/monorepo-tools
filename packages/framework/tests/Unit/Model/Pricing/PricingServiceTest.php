<?php

namespace Tests\FrameworkBundle\Unit\Model\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\PricingService;

class PricingServiceTest extends TestCase
{
    /**
     * @dataProvider getArePricesDifferentProvider
     * @param array $prices
     * @param mixed $arePricesDifferent
     */
    public function testArePricesDifferent(array $prices, $arePricesDifferent)
    {
        $pricingService = new PricingService();

        $this->assertSame($arePricesDifferent, $pricingService->arePricesDifferent($prices));
    }

    public function getArePricesDifferentProvider()
    {
        return [
            [
                'prices' => [
                    new Price(100, 120),
                    new Price(100, 120),
                ],
                'arePricesDifferent' => false,
            ],
            [
                'prices' => [
                    new Price(100, 120),
                ],
                'arePricesDifferent' => false,
            ],
            [
                'prices' => [
                    new Price(100, 120),
                    new Price('100', '120'),
                ],
                'arePricesDifferent' => true,
            ],
            [
                'prices' => [
                    new Price(200, 240),
                    new Price(100, 120),
                ],
                'arePricesDifferent' => true,
            ],
        ];
    }

    public function testArePricesDifferentEmptyArray()
    {
        $pricingService = new PricingService();

        $this->expectException(\Shopsys\FrameworkBundle\Model\Pricing\Exception\InvalidArgumentException::class);
        $pricingService->arePricesDifferent([]);
    }
}

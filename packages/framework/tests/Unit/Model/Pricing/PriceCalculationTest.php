<?php

namespace Tests\FrameworkBundle\Unit\Model\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;

class PriceCalculationTest extends TestCase
{
    public function applyVatPercentProvider()
    {
        return [
            [
                'priceWithoutVat' => '0',
                'vatPercent' => '21',
                'expectedPriceWithVat' => '0',
            ],
            [
                'priceWithoutVat' => '100',
                'vatPercent' => '0',
                'expectedPriceWithVat' => '100',
            ],
            [
                'priceWithoutVat' => '100',
                'vatPercent' => '21',
                'expectedPriceWithVat' => '121',
            ],
            [
                'priceWithoutVat' => '100.9',
                'vatPercent' => '21.1',
                'expectedPriceWithVat' => '122.1899',
            ],
        ];
    }

    /**
     * @dataProvider applyVatPercentProvider
     */
    public function testApplyVatPercent(
        $priceWithoutVat,
        $vatPercent,
        $expectedPriceWithVat
    ) {
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getRoundingType'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock
            ->expects($this->any())->method('getRoundingType')
                ->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));

        $rounding = new Rounding($pricingSettingMock);
        $priceCalculation = new PriceCalculation($rounding);
        $vatData = new VatData();
        $vatData->name = 'testVat';
        $vatData->percent = $vatPercent;
        $vat = new Vat($vatData);

        $actualPriceWithVat = $priceCalculation->applyVatPercent($priceWithoutVat, $vat);

        $this->assertSame(round($expectedPriceWithVat, 6), round($actualPriceWithVat, 6));
    }

    public function getVatAmountByPriceWithVatProvider()
    {
        return [
            [
                'priceWithVat' => '0',
                'vatPercent' => '10',
                'expectedVatAmount' => '0',
            ],
            [
                'priceWithoutVat' => '100',
                'vatPercent' => '0',
                'expectedPriceWithVat' => '0',
            ],
            [
                'priceWithoutVat' => '100',
                'vatPercent' => '21',
                'expectedPriceWithVat' => '17.36',
            ],
        ];
    }

    /**
     * @dataProvider getVatAmountByPriceWithVatProvider
     */
    public function testGetVatAmountByPriceWithVat(
        $priceWithVat,
        $vatPercent,
        $expectedVatAmount
    ) {
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getRoundingType'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock
            ->expects($this->any())->method('getRoundingType')
                ->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));

        $rounding = new Rounding($pricingSettingMock);
        $priceCalculation = new PriceCalculation($rounding);
        $vatData = new VatData();
        $vatData->name = 'testVat';
        $vatData->percent = $vatPercent;
        $vat = new Vat($vatData);

        $actualVatAmount = $priceCalculation->getVatAmountByPriceWithVat($priceWithVat, $vat);

        $this->assertSame(round($expectedVatAmount, 6), round($actualVatAmount, 6));
    }
}

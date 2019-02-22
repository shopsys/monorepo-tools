<?php

namespace Tests\FrameworkBundle\Unit\Model\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Tests\FrameworkBundle\Test\IsMoneyEqual;

class PriceCalculationTest extends TestCase
{
    public function applyVatPercentProvider()
    {
        return [
            [
                'priceWithoutVat' => Money::create(0),
                'vatPercent' => '21',
                'expectedPriceWithVat' => Money::create(0),
            ],
            [
                'priceWithoutVat' => Money::create(100),
                'vatPercent' => '0',
                'expectedPriceWithVat' => Money::create(100),
            ],
            [
                'priceWithoutVat' => Money::create(100),
                'vatPercent' => '21',
                'expectedPriceWithVat' => Money::create(121),
            ],
            [
                'priceWithoutVat' => Money::create('100.9'),
                'vatPercent' => '21.1',
                'expectedPriceWithVat' => Money::create('122.1899'),
            ],
        ];
    }

    /**
     * @dataProvider applyVatPercentProvider
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithoutVat
     * @param string $vatPercent
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $expectedPriceWithVat
     */
    public function testApplyVatPercent(
        Money $priceWithoutVat,
        string $vatPercent,
        Money $expectedPriceWithVat
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

        $this->assertThat($actualPriceWithVat, new IsMoneyEqual($expectedPriceWithVat));
    }

    public function getVatAmountByPriceWithVatProvider()
    {
        return [
            [
                'priceWithVat' => Money::create(0),
                'vatPercent' => '10',
                'expectedVatAmount' => Money::create(0),
            ],
            [
                'priceWithoutVat' => Money::create(100),
                'vatPercent' => '0',
                'expectedPriceWithVat' => Money::create(0),
            ],
            [
                'priceWithoutVat' => Money::create(100),
                'vatPercent' => '21',
                'expectedPriceWithVat' => Money::create('17.36'),
            ],
        ];
    }

    /**
     * @dataProvider getVatAmountByPriceWithVatProvider
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithVat
     * @param string $vatPercent
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $expectedVatAmount
     */
    public function testGetVatAmountByPriceWithVat(
        Money $priceWithVat,
        string $vatPercent,
        Money $expectedVatAmount
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

        $this->assertThat($actualVatAmount, new IsMoneyEqual($expectedVatAmount));
    }
}

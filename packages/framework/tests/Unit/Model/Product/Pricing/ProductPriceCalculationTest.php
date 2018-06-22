<?php

namespace Tests\FrameworkBundle\Unit\Model\Product\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingService;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceRepository;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ProductPriceCalculationTest extends TestCase
{
    public function calculatePriceProvider()
    {
        return [
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'inputPrice' => '6999',
                'vatPercent' => '21',
                'pricingGroupCoefficient' => '1',
                'priceWithoutVat' => '6998.78',
                'priceWithVat' => '8469',
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
                'inputPrice' => '6999.99',
                'vatPercent' => '21',
                'pricingGroupCoefficient' => '2',
                'priceWithoutVat' => '11569.6',
                'priceWithVat' => '14000',
            ],
        ];
    }

    /**
     * @param int $inputPriceType
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $variants
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation
     */
    private function getProductPriceCalculationWithInputPriceTypeAndVariants($inputPriceType, $variants)
    {
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getInputPriceType', 'getRoundingType', 'getDomainDefaultCurrencyIdByDomainId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock
            ->expects($this->any())->method('getInputPriceType')
                ->will($this->returnValue($inputPriceType));
        $pricingSettingMock
            ->expects($this->any())->method('getRoundingType')
                ->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));
        $pricingSettingMock
            ->expects($this->any())->method('getDomainDefaultCurrencyIdByDomainId')
                ->will($this->returnValue(1));

        $productManualInputPriceRepositoryMock = $this->getMockBuilder(ProductManualInputPriceRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $currencyFacadeMock = $this->getMockBuilder(CurrencyFacade::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMock();

        $currencyMock = $this->getMockBuilder(Currency::class)
            ->setMethods(['getReversedExchangeRate'])
            ->disableOriginalConstructor()
            ->getMock();

        $currencyMock
            ->expects($this->any())->method('getReversedExchangeRate')
                ->will($this->returnValue(1));

        $currencyFacadeMock
            ->expects($this->any())->method('getById')
            ->will($this->returnValue($currencyMock));

        $productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->setMethods(['getAllSellableVariantsByMainVariant'])
            ->disableOriginalConstructor()
            ->getMock();
        $productRepositoryMock
            ->expects($this->any())->method('getAllSellableVariantsByMainVariant')
            ->will($this->returnValue($variants));

        $pricingService = new PricingService();

        $rounding = new Rounding($pricingSettingMock);
        $priceCalculation = new PriceCalculation($rounding);
        $basePriceCalculation = new BasePriceCalculation($priceCalculation, $rounding);

        return new ProductPriceCalculation(
            $basePriceCalculation,
            $pricingSettingMock,
            $productManualInputPriceRepositoryMock,
            $currencyFacadeMock,
            $productRepositoryMock,
            $pricingService
        );
    }

    /**
     * @param string $inputPrice
     * @param string $vatPercent
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    private function getProductWithInputPriceAndVatPercentAndAutoCalculationPriceType(
        $inputPrice,
        $vatPercent
    ) {
        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = $vatPercent;
        $vat = new Vat($vatData);

        $productData = new ProductData();
        $productData->name = ['cs' => 'anyProductName'];
        $productData->price = $inputPrice;
        $productData->vat = $vat;

        return Product::create($productData);
    }

    /**
     * @dataProvider calculatePriceProvider
     */
    public function testCalculatePriceWithAutoCalculationPriceType(
        $inputPriceType,
        $inputPrice,
        $vatPercent,
        $pricingGroupCoefficient,
        $priceWithoutVat,
        $priceWithVat
    ) {
        $productPriceCalculation = $this->getProductPriceCalculationWithInputPriceTypeAndVariants(
            $inputPriceType,
            []
        );

        $product = $this->getProductWithInputPriceAndVatPercentAndAutoCalculationPriceType(
            $inputPrice,
            $vatPercent
        );

        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroupData->coefficient = $pricingGroupCoefficient;
        $pricingGroup = new PricingGroup($pricingGroupData, 1);

        $productPrice = $productPriceCalculation->calculatePrice($product, $pricingGroup->getDomainId(), $pricingGroup);

        $this->assertSame(round($priceWithoutVat, 6), round($productPrice->getPriceWithoutVat(), 6));
        $this->assertSame(round($priceWithVat, 6), round($productPrice->getPriceWithVat(), 6));
    }

    public function calculatePriceMainVariantProvider()
    {
        $vatPercent = 10;

        return [
            [
                'variants' => [
                    $this->getProductWithInputPriceAndVatPercentAndAutoCalculationPriceType('100', $vatPercent),
                    $this->getProductWithInputPriceAndVatPercentAndAutoCalculationPriceType('200', $vatPercent),
                ],
                'expectedPriceWithVat' => 100,
                'expectedFrom' => true,
            ],
            [
                'variants' => [
                    $this->getProductWithInputPriceAndVatPercentAndAutoCalculationPriceType('200', $vatPercent),
                    $this->getProductWithInputPriceAndVatPercentAndAutoCalculationPriceType('200', $vatPercent),
                ],
                'expectedPriceWithVat' => 200,
                'expectedFrom' => false,
            ],
        ];
    }

    /**
     * @dataProvider calculatePriceMainVariantProvider
     */
    public function testCalculatePriceOfMainVariantWithVariantsAndAutoCalculationPriceType(
        $variants,
        $expectedPriceWithVat,
        $expectedFrom
    ) {
        $productPriceCalculation = $this->getProductPriceCalculationWithInputPriceTypeAndVariants(
            PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
            $variants
        );

        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroupData->coefficient = 1;
        $pricingGroup = new PricingGroup($pricingGroupData, 1);

        $product = Product::createMainVariant(new ProductData(), $variants);

        $productPrice = $productPriceCalculation->calculatePrice($product, $pricingGroup->getDomainId(), $pricingGroup);
        /* @var $productPrice \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice */

        $this->assertSame(round($expectedPriceWithVat, 6), round($productPrice->getPriceWithVat(), 6));
        $this->assertSame($expectedFrom, $productPrice->isPriceFrom());
    }

    public function testCalculatePriceOfMainVariantWithoutAnySellableVariants()
    {
        $productPriceCalculation = $this->getProductPriceCalculationWithInputPriceTypeAndVariants(
            PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
            []
        );

        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroupData->coefficient = 1;
        $pricingGroup = new PricingGroup($pricingGroupData, 1);

        $variant = Product::create(new ProductData());
        $product = Product::createMainVariant(new ProductData(), [$variant]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException::class);

        $productPriceCalculation->calculatePrice($product, $pricingGroup->getDomainId(), $pricingGroup);
    }
}

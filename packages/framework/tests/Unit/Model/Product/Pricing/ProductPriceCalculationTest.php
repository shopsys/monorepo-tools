<?php

namespace Tests\FrameworkBundle\Unit\Model\Product\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingService;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceRepository;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ProductPriceCalculationTest extends TestCase
{
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
            $productRepositoryMock,
            $pricingService
        );
    }

    public function testCalculatePriceOfMainVariantWithoutAnySellableVariants()
    {
        $productPriceCalculation = $this->getProductPriceCalculationWithInputPriceTypeAndVariants(
            PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
            []
        );

        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroup = new PricingGroup($pricingGroupData, 1);

        $variant = Product::create(new ProductData());
        $product = Product::createMainVariant(new ProductData(), [$variant]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException::class);

        $productPriceCalculation->calculatePrice($product, $pricingGroup->getDomainId(), $pricingGroup);
    }
}

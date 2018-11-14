<?php

namespace Tests\FrameworkBundle\Unit\Model\Product\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceService;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

class ProductInputPriceServiceTest extends TestCase
{
    public function testGetManualInputPricesDataWithManualCalculationType()
    {
        $productData = new ProductData();
        $vatData = new VatData();
        $vatData->name = 'VatName';
        $vatData->percent = '10.0';
        $productData->vat = new Vat($vatData);
        $product = Product::create($productData);

        $pricingGroupMock1 = $this->getMockBuilder(PricingGroup::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingGroupMock1->expects($this->any())->method('getId')->willReturn(1);
        $pricingGroupMock2 = $this->getMockBuilder(PricingGroup::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingGroupMock2->expects($this->any())->method('getId')->willReturn(2);
        $pricingGroups = [$pricingGroupMock1, $pricingGroupMock2];

        $manualInputPrices = [
            new ProductManualInputPrice($product, $pricingGroups[0], '1000'),
            new ProductManualInputPrice($product, $pricingGroups[1], '2000'),
        ];

        $productInputPriceService = new ProductInputPriceService();
        $manualInputPricesData = $productInputPriceService->getManualInputPricesDataIndexedByPricingGroupId(
            $manualInputPrices
        );

        $this->assertCount(2, $manualInputPricesData);
        $this->assertSame('1000', $manualInputPricesData[$pricingGroups[0]->getId()]);
        $this->assertSame('2000', $manualInputPricesData[$pricingGroups[1]->getId()]);
    }
}

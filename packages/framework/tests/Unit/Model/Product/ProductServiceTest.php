<?php

namespace Tests\FrameworkBundle\Unit\Model\Product;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

class ProductServiceTest extends TestCase
{
    public function testEditSchedulesPriceRecalculation()
    {
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock->expects($this->once())->method('scheduleProductForImmediateRecalculation');

        $productData = new ProductData();
        $product = Product::create($productData);

        $product->edit(new ProductCategoryDomainFactory(), $productData, $productPriceRecalculationSchedulerMock);
    }
}

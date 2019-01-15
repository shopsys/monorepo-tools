<?php

namespace Tests\FrameworkBundle\Unit\Model\Product;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Product\Availability\Availability;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityCalculation;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductFactory;

class ProductFactoryTest extends TestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFactory
     */
    protected $productFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->productFactory = new ProductFactory(new EntityNameResolver([]), $this->getProductAvailabilityCalculationMock(), new ProductCategoryDomainFactory());
        parent::setUp();
    }

    public function testCreateVariant()
    {
        $mainVariantData = new ProductData();
        $mainProduct = Product::create(new ProductData(), new ProductCategoryDomainFactory());
        $variants = [];

        $mainVariant = $this->productFactory->createMainVariant($mainVariantData, $mainProduct, $variants);

        $this->assertNotSame($mainProduct, $mainVariant);
        $this->assertTrue(in_array($mainProduct, $mainVariant->getVariants(), true));
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityCalculation|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getProductAvailabilityCalculationMock()
    {
        $dummyAvailability = new Availability(new AvailabilityData());
        $productAvailabilityCalculationMock = $this->getMockBuilder(ProductAvailabilityCalculation::class)
            ->disableOriginalConstructor()
            ->setMethods(['calculateAvailability'])
            ->getMock();
        $productAvailabilityCalculationMock
            ->expects($this->any())
            ->method('calculateAvailability')
            ->willReturn($dummyAvailability);

        return $productAvailabilityCalculationMock;
    }
}

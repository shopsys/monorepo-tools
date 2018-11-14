<?php

namespace Tests\FrameworkBundle\Unit\Model\Product;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Product\Availability\Availability;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityCalculation;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductVariantService;

class ProductVariantServiceTest extends TestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVariantService
     */
    protected $productVariantService;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $productFactory = new ProductFactory(new EntityNameResolver([]), $this->getProductAvailabilityCalculationMock());
        $this->productVariantService = new ProductVariantService($productFactory);
        parent::setUp();
    }

    public function testCheckProductIsNotMainVariantException()
    {
        $productData = new ProductData();
        $variant = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsAlreadyMainVariantException::class);
        $this->productVariantService->checkProductIsNotMainVariant($mainVariant);
    }

    public function testRefreshProductVariants()
    {
        $productData = new ProductData();
        $variant1 = Product::create($productData);
        $variant2 = Product::create($productData);
        $variant3 = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant1, $variant2]);

        $currentVariants = [$variant2, $variant3];
        $this->productVariantService->refreshProductVariants($mainVariant, $currentVariants);

        $variantsArray = $mainVariant->getVariants();

        $this->assertNotContains($variant1, $variantsArray);
        $this->assertContains($variant2, $variantsArray);
        $this->assertContains($variant3, $variantsArray);
    }

    public function testCreateVariant()
    {
        $mainVariantData = new ProductData();
        $mainProduct = Product::create(new ProductData());
        $variants = [];

        $mainVariant = $this->productVariantService->createMainVariant($mainVariantData, $mainProduct, $variants);

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

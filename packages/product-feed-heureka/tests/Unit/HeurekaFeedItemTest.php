<?php

namespace Tests\ProductFeed\HeurekaBundle\Unit;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Availability\Availability;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaFeedItem;
use Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaFeedItemFactory;
use Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaProductDataBatchLoader;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade;

class HeurekaFeedItemTest extends TestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser|\PHPUnit\Framework\MockObject\MockObject
     */
    private $productPriceCalculationForUserMock;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaProductDataBatchLoader|\PHPUnit\Framework\MockObject\MockObject
     */
    private $heurekaProductDataBatchLoaderMock;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade|\PHPUnit\Framework\MockObject\MockObject
     */
    private $heurekaCategoryFacadeMock;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade|\PHPUnit\Framework\MockObject\MockObject
     */
    private $categoryFacadeMock;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaFeedItemFactory::__construct
     */
    private $heurekaFeedItemFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    private $defaultDomain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product|\PHPUnit\Framework\MockObject\MockObject
     */
    private $defaultProduct;

    public function setUp()
    {
        $this->productPriceCalculationForUserMock = $this->createMock(ProductPriceCalculationForUser::class);
        $this->heurekaProductDataBatchLoaderMock = $this->createMock(HeurekaProductDataBatchLoader::class);
        $this->heurekaCategoryFacadeMock = $this->createMock(HeurekaCategoryFacade::class);
        $this->categoryFacadeMock = $this->createMock(CategoryFacade::class);

        $this->heurekaFeedItemFactory = new HeurekaFeedItemFactory(
            $this->productPriceCalculationForUserMock,
            $this->heurekaProductDataBatchLoaderMock,
            $this->heurekaCategoryFacadeMock,
            $this->categoryFacadeMock
        );

        $this->defaultDomain = $this->createDomainConfigMock(1, 'https://example.cz', 'cs');

        $this->defaultProduct = $this->createMock(Product::class);
        $this->defaultProduct->method('getId')->willReturn(1);
        $this->defaultProduct->method('getName')->with('cs')->willReturn('product name');

        $availabilityMock = $this->createMock(Availability::class);
        /* @var \Shopsys\FrameworkBundle\Model\Product\Availability\Availability|\PHPUnit\Framework\MockObject\MockObject $availabilityMock */
        $availabilityMock->method('getDispatchTime')->willReturn(0);
        $this->defaultProduct->method('getCalculatedAvailability')->willReturn($availabilityMock);

        $productPrice = new ProductPrice(new Price(Money::zero(), Money::zero()), false);
        $this->productPriceCalculationForUserMock->method('calculatePriceForUserAndDomainId')
            ->with($this->defaultProduct, 1, null)->willReturn($productPrice);

        $this->heurekaProductDataBatchLoaderMock->method('getProductUrl')
            ->with($this->defaultProduct, $this->defaultDomain)->willReturn('https://example.com/product-1');
    }

    /**
     * @param int $id
     * @param string $url
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createDomainConfigMock(int $id, string $url, string $locale): DomainConfig
    {
        $domainConfigMock = $this->createMock(DomainConfig::class);

        $domainConfigMock->method('getId')->willReturn($id);
        $domainConfigMock->method('getUrl')->willReturn($url);
        $domainConfigMock->method('getLocale')->willReturn($locale);

        return $domainConfigMock;
    }

    public function testMinimalHeurekaFeedItemIsCreatable()
    {
        $heurekaFeedItem = $this->heurekaFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertInstanceOf(HeurekaFeedItem::class, $heurekaFeedItem);

        self::assertEquals(1, $heurekaFeedItem->getId());
        self::assertEquals(1, $heurekaFeedItem->getSeekId());
        self::assertNull($heurekaFeedItem->getGroupId());
        self::assertEquals('product name', $heurekaFeedItem->getName());
        self::assertNull($heurekaFeedItem->getDescription());
        self::assertEquals('https://example.com/product-1', $heurekaFeedItem->getUrl());
        self::assertNull($heurekaFeedItem->getImgUrl());
        self::assertEquals(0, $heurekaFeedItem->getPrice()->getPriceWithoutVat());
        self::assertEquals(0, $heurekaFeedItem->getPrice()->getPriceWithVat());
        self::assertNull($heurekaFeedItem->getEan());
        self::assertEquals(0, $heurekaFeedItem->getDeliveryDate());
        self::assertNull($heurekaFeedItem->getManufacturer());
        self::assertNull($heurekaFeedItem->getCategoryText());
        self::assertEquals([], $heurekaFeedItem->getParams());
        self::assertNull($heurekaFeedItem->getCpc());
    }

    public function testHeurekaFeedItemWithGroupId()
    {
        $mainVariantMock = $this->createMock(Product::class);
        $mainVariantMock->method('getId')->willReturn(2);
        $this->defaultProduct->method('isVariant')->willReturn(true);
        $this->defaultProduct->method('getMainVariant')->willReturn($mainVariantMock);

        $heurekaFeedItem = $this->heurekaFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals(2, $heurekaFeedItem->getGroupId());
    }

    public function testHeurekaFeedItemWithDescription()
    {
        $this->defaultProduct->method('getDescription')
            ->with(1)->willReturn('product description');

        $heurekaFeedItem = $this->heurekaFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals('product description', $heurekaFeedItem->getDescription());
    }

    public function testHeurekaFeedItemWithImgUrl()
    {
        $this->heurekaProductDataBatchLoaderMock->method('getProductImageUrl')
            ->with($this->defaultProduct, $this->defaultDomain)->willReturn('https://example.com/img/product/1');

        $heurekaFeedItem = $this->heurekaFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals('https://example.com/img/product/1', $heurekaFeedItem->getImgUrl());
    }

    public function testHeurekaFeedItemWithEan()
    {
        $this->defaultProduct->method('getEan')->willReturn('1234567890123');

        $heurekaFeedItem = $this->heurekaFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals('1234567890123', $heurekaFeedItem->getEan());
    }

    public function testHeurekaFeedItemWithManufacturer()
    {
        $brand = $this->createMock(Brand::class);
        /* @var \Shopsys\FrameworkBundle\Model\Product\Brand\Brand|\PHPUnit\Framework\MockObject\MockObject $brand */
        $brand->method('getName')->willReturn('manufacturer name');
        $this->defaultProduct->method('getBrand')->willReturn($brand);

        $heurekaFeedItem = $this->heurekaFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals('manufacturer name', $heurekaFeedItem->getManufacturer());
    }

    public function testHeurekaFeedItemWithCategoryText()
    {
        $heurekaCategoryMock = $this->createMock(HeurekaCategory::class);
        /* @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory|\PHPUnit\Framework\MockObject\MockObject $heurekaCategoryMock */
        $heurekaCategoryMock->method('getFullName')->willReturn('heureka category full text');

        $categoryMock = $this->createMock(Category::class);
        /* @var \Shopsys\FrameworkBundle\Model\Category\Category|\PHPUnit\Framework\MockObject\MockObject $categoryMock */
        $categoryMock->method('getId')->willReturn(1);

        $this->categoryFacadeMock->method('findProductMainCategoryByDomainId')
            ->with($this->defaultProduct, $this->defaultDomain->getId())->willReturn($categoryMock);

        $this->heurekaCategoryFacadeMock->method('findByCategoryId')
            ->with(1)->willReturn($heurekaCategoryMock);

        $heurekaFeedItem = $this->heurekaFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals('heureka category full text', $heurekaFeedItem->getCategoryText());
    }

    public function testHeurekaFeedItemWithParams()
    {
        $this->heurekaProductDataBatchLoaderMock->method('getProductParametersByName')
            ->with($this->defaultProduct, $this->defaultDomain)->willReturn(['color' => 'black']);

        $heurekaFeedItem = $this->heurekaFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals(['color' => 'black'], $heurekaFeedItem->getParams());
    }

    public function testHeurekaFeedItemWithCpc()
    {
        $this->heurekaProductDataBatchLoaderMock->method('getProductCpc')
            ->with($this->defaultProduct, $this->defaultDomain)->willReturn(5.0);

        $heurekaFeedItem = $this->heurekaFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals(5.0, $heurekaFeedItem->getCpc());
    }
}

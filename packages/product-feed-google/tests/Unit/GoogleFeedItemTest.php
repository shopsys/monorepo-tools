<?php

namespace Tests\ProductFeed\GoogleBundle\Unit;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Collection\ProductUrlsBatchLoader;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ProductFeed\GoogleBundle\Model\FeedItem\GoogleFeedItem;
use Shopsys\ProductFeed\GoogleBundle\Model\FeedItem\GoogleFeedItemFactory;

class GoogleFeedItemTest extends TestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser|\PHPUnit\Framework\MockObject\MockObject
     */
    private $productPriceCalculationForUserMock;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade|\PHPUnit\Framework\MockObject\MockObject
     */
    private $currencyFacadeMock;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Collection\ProductUrlsBatchLoader|\PHPUnit\Framework\MockObject\MockObject
     */
    private $productUrlsBatchLoaderMock;

    /**
     * @var \Shopsys\ProductFeed\GoogleBundle\Model\FeedItem\GoogleFeedItemFactory
     */
    private $googleFeedItemFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency|\PHPUnit\Framework\MockObject\MockObject
     */
    private $defaultCurrency;

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
        $this->currencyFacadeMock = $this->createMock(CurrencyFacade::class);
        $this->productUrlsBatchLoaderMock = $this->createMock(ProductUrlsBatchLoader::class);

        $this->googleFeedItemFactory = new GoogleFeedItemFactory(
            $this->productPriceCalculationForUserMock,
            $this->currencyFacadeMock,
            $this->productUrlsBatchLoaderMock
        );

        $this->defaultCurrency = $this->createCurrencyMock(1, 'EUR');
        $this->defaultDomain = $this->createDomainConfigMock(1, 'https://example.com', 'en', $this->defaultCurrency);

        $this->defaultProduct = $this->createMock(Product::class);
        $this->defaultProduct->method('getId')->willReturn(1);
        $this->defaultProduct->method('getName')->with('en')->willReturn('product name');
        $this->defaultProduct->method('isSellingDenied')->willReturn(false);

        $this->mockProductPrice($this->defaultProduct, $this->defaultDomain, new Price(Money::zero(), Money::zero()));
        $this->mockProductUrl($this->defaultProduct, $this->defaultDomain, 'https://example.com/product-1');
    }

    /**
     * @param int $id
     * @param string $code
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency|\PHPUnit\Framework\MockObject\MockObject
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    private function createCurrencyMock(int $id, string $code): Currency
    {
        $currencyMock = $this->createMock(Currency::class);

        $currencyMock->method('getId')->willReturn($id);
        $currencyMock->method('getCode')->willReturn($code);

        return $currencyMock;
    }

    /**
     * @param int $id
     * @param string $url
     * @param string $locale
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createDomainConfigMock(int $id, string $url, string $locale, Currency $currency): DomainConfig
    {
        $domainConfigMock = $this->createMock(DomainConfig::class);

        $domainConfigMock->method('getId')->willReturn($id);
        $domainConfigMock->method('getUrl')->willReturn($url);
        $domainConfigMock->method('getLocale')->willReturn($locale);

        $this->currencyFacadeMock->method('getDomainDefaultCurrencyByDomainId')
            ->with($id)->willReturn($currency);

        return $domainConfigMock;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domain
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     */
    private function mockProductPrice(Product $product, DomainConfig $domain, Price $price): void
    {
        $productPrice = new ProductPrice($price, false);
        $this->productPriceCalculationForUserMock->method('calculatePriceForUserAndDomainId')
            ->with($product, $domain->getId(), null)->willReturn($productPrice);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domain
     * @param string $url
     */
    private function mockProductUrl(Product $product, DomainConfig $domain, string $url): void
    {
        $this->productUrlsBatchLoaderMock->method('getProductUrl')
            ->with($product, $domain)->willReturn($url);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domain
     * @param string $url
     */
    private function mockProductImageUrl(Product $product, DomainConfig $domain, string $url): void
    {
        $this->productUrlsBatchLoaderMock->method('getProductImageUrl')
            ->with($product, $domain)->willReturn($url);
    }

    public function testMinimalGoogleFeedItemIsCreatable()
    {
        $googleFeedItem = $this->googleFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertInstanceOf(GoogleFeedItem::class, $googleFeedItem);

        self::assertEquals(1, $googleFeedItem->getId());
        self::assertEquals(1, $googleFeedItem->getSeekId());
        self::assertEquals('product name', $googleFeedItem->getTitle());
        self::assertNull($googleFeedItem->getBrand());
        self::assertNull($googleFeedItem->getDescription());
        self::assertEquals('https://example.com/product-1', $googleFeedItem->getLink());
        self::assertNull($googleFeedItem->getImageLink());
        self::assertEquals('in stock', $googleFeedItem->getAvailability());
        self::assertEquals(0, $googleFeedItem->getPrice()->getPriceWithoutVat());
        self::assertEquals(0, $googleFeedItem->getPrice()->getPriceWithVat());
        self::assertEquals('EUR', $googleFeedItem->getCurrency()->getCode());
        self::assertEquals([], $googleFeedItem->getIdentifiers());
    }

    public function testGoogleFeedItemWithBrand()
    {
        $brand = $this->createMock(Brand::class);
        /* @var \Shopsys\FrameworkBundle\Model\Product\Brand\Brand|\PHPUnit\Framework\MockObject\MockObject $brand */
        $brand->method('getName')->willReturn('brand name');
        $this->defaultProduct->method('getBrand')->willReturn($brand);

        $googleFeedItem = $this->googleFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals('brand name', $googleFeedItem->getBrand());
    }

    public function testGoogleFeedItemWithDescription()
    {
        $this->defaultProduct->method('getDescription')
            ->with(1)->willReturn('product description');

        $googleFeedItem = $this->googleFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals('product description', $googleFeedItem->getDescription());
    }

    public function testGoogleFeedItemWithImageLink()
    {
        $this->mockProductImageUrl($this->defaultProduct, $this->defaultDomain, 'https://example.com/img/product/1');

        $googleFeedItem = $this->googleFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals('https://example.com/img/product/1', $googleFeedItem->getImageLink());
    }

    public function testGoogleFeedItemWithSellingDenied()
    {
        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(1);
        $product->method('getName')->with('en')->willReturn('product name');
        $product->method('isSellingDenied')->willReturn(true);

        $googleFeedItem = $this->googleFeedItemFactory->create($product, $this->defaultDomain);

        self::assertEquals('out of stock', $googleFeedItem->getAvailability());
    }

    public function testGoogleFeedItemWithEan()
    {
        $this->defaultProduct->method('getEan')->willReturn('1234567890123');

        $googleFeedItem = $this->googleFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals(['gtin' => '1234567890123'], $googleFeedItem->getIdentifiers());
    }

    public function testGoogleFeedItemWithPartno()
    {
        $this->defaultProduct->method('getPartno')->willReturn('HSC0424PP');

        $googleFeedItem = $this->googleFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals(['mpn' => 'HSC0424PP'], $googleFeedItem->getIdentifiers());
    }

    public function testGoogleFeedItemWithEanAndPartno()
    {
        $this->defaultProduct->method('getEan')->willReturn('1234567890123');
        $this->defaultProduct->method('getPartno')->willReturn('HSC0424PP');

        $googleFeedItem = $this->googleFeedItemFactory->create($this->defaultProduct, $this->defaultDomain);

        self::assertEquals(['gtin' => '1234567890123', 'mpn' => 'HSC0424PP'], $googleFeedItem->getIdentifiers());
    }
}

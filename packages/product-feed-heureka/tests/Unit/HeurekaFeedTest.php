<?php

namespace Tests\ProductFeed\HeurekaBundle\Unit;

use DOMDocument;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\HeurekaBundle\HeurekaFeedConfig;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade;
use Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomain;
use Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade;
use Twig_Environment;
use Twig_Loader_Filesystem;

class HeurekaFeedTest extends TestCase
{
    const DOMAIN_ID_FIRST = 1;
    const DOMAIN_ID_SECOND = 2;

    const PRODUCT_ID_FIRST = 1;
    const PRODUCT_ID_SECOND = 2;
    const PRODUCT_ID_THIRD = 3;

    const CATEGORY_ID_FIRST = 1;
    const CATEGORY_ID_SECOND = 2;

    const HEUREKA_CATEGORY_ID_FIRST = 1;

    const EXPECTED_XML_FILE_NAME = 'test.xml';

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\HeurekaFeedConfig
     */
    private $heurekaFeedConfig;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomain[]
     */
    private $heurekaProductDomainsForDomainFirst;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomain[]
     */
    private $heurekaProductDomainsForDomainSecond;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory
     */
    private $heurekaCategory;

    /**
     * @var array
     */
    private $productsIds;

    public function setUp()
    {
        $this->initTestData();

        $heurekaProductDomainFacadeMock = $this->createHeurekaProductDomainFacadeMock();

        $heurekaCategoryFacadeMock = $this->createHeurekaCategoryFacadeMock();

        $this->heurekaFeedConfig = new HeurekaFeedConfig($heurekaProductDomainFacadeMock, $heurekaCategoryFacadeMock);

        $twigLoader = new Twig_Loader_Filesystem([__DIR__ . '/../../src/Resources/views']);
        $this->twig = new Twig_Environment($twigLoader);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade
     */
    private function createHeurekaProductDomainFacadeMock()
    {
        $returnCallback = function ($productsIds, $domainId) {
            if ($productsIds === $this->productsIds && $domainId === self::DOMAIN_ID_FIRST) {
                return $this->heurekaProductDomainsForDomainFirst;
            } elseif ($productsIds === $this->productsIds && $domainId === self::DOMAIN_ID_SECOND) {
                return $this->heurekaProductDomainsForDomainSecond;
            }
            return [];
        };

        /** @var HeurekaProductDomainFacade|\PHPUnit\Framework\MockObject\MockObject $heurekaProductDomainFacadeMock */
        $heurekaProductDomainFacadeMock = $this->createMock(HeurekaProductDomainFacade::class);

        $heurekaProductDomainFacadeMock->method('getHeurekaProductDomainsByProductsIdsDomainIdIndexedByProductId')
            ->willReturnCallback($returnCallback);

        return $heurekaProductDomainFacadeMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade
     */
    private function createHeurekaCategoryFacadeMock()
    {
        $returnCallback = function ($categoryId) {
            if ($categoryId === self::CATEGORY_ID_FIRST) {
                return $this->heurekaCategory;
            }
            return null;
        };

        /** @var HeurekaCategoryFacade|\PHPUnit\Framework\MockObject\MockObject $heurekaCategoryFacadeMock */
        $heurekaCategoryFacadeMock = $this->createMock(HeurekaCategoryFacade::class);

        $heurekaCategoryFacadeMock
            ->method('findByCategoryId')
            ->willReturnCallback($returnCallback);

        return $heurekaCategoryFacadeMock;
    }

    private function initTestData()
    {
        $this->productsIds = [self::PRODUCT_ID_FIRST, self::PRODUCT_ID_SECOND];

        $firstProductMock = $this->createProductMock(self::PRODUCT_ID_FIRST);

        $heurekaProductDomainForFirstProductFirstDomainMock = $this->createHeurekaProductDomainMock(
            $firstProductMock,
            self::DOMAIN_ID_FIRST,
            7.5
        );

        $this->heurekaProductDomainsForDomainFirst[self::PRODUCT_ID_FIRST] = $heurekaProductDomainForFirstProductFirstDomainMock;

        $heurekaProductDomainForFirstProductSecondDomainMock = $this->createHeurekaProductDomainMock(
            $firstProductMock,
            self::DOMAIN_ID_SECOND,
            null
        );

        $this->heurekaProductDomainsForDomainSecond[self::PRODUCT_ID_FIRST] = $heurekaProductDomainForFirstProductSecondDomainMock;

        $secondProductMock = $this->createProductMock(self::PRODUCT_ID_SECOND);

        $heurekaProductDomainForSecondProductFirstDomainMock = $this->createHeurekaProductDomainMock(
            $secondProductMock,
            self::DOMAIN_ID_FIRST,
            null
        );

        $this->heurekaProductDomainsForDomainFirst[self::PRODUCT_ID_SECOND] = $heurekaProductDomainForSecondProductFirstDomainMock;

        $heurekaProductDomainForSecondProductSecondDomainMock = $this->createHeurekaProductDomainMock(
            $secondProductMock,
            self::DOMAIN_ID_SECOND,
            null
        );

        $this->heurekaProductDomainsForDomainSecond[self::PRODUCT_ID_SECOND] = $heurekaProductDomainForSecondProductSecondDomainMock;

        $this->heurekaCategory = $this->getMockBuilder(HeurekaCategory::class)
            ->setMethods(['getFullName'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->heurekaCategory->method('getFullName')->willReturn('fullCategoryName');
    }

    /**
     * @param int $productId
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createProductMock($productId)
    {
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->method('getId')->willReturn($productId);

        return $productMock;
    }

    /**
     * @param \PHPUnit\Framework\MockObject\MockObject $productMock
     * @param int $domainId
     * @param float|null $cpc
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createHeurekaProductDomainMock(MockObject $productMock, $domainId, $cpc)
    {
        $heurekaProductDomainMock = $this->getMockBuilder(HeurekaProductDomain::class)
            ->setMethods(['getProduct', 'getDomainId', 'getCpc'])
            ->disableOriginalConstructor()
            ->getMock();
        $heurekaProductDomainMock->method('getProduct')->willReturn($productMock);
        $heurekaProductDomainMock->method('getDomainId')->willReturn($domainId);
        $heurekaProductDomainMock->method('getCpc')->willReturn($cpc);

        return $heurekaProductDomainMock;
    }

    public function testGeneratingOfFeed()
    {
        $feedItems = $this->getFeedItemsData();
        $domainConfigMock = $this->createDomainConfigMock(self::DOMAIN_ID_FIRST, 'http://www.example.com/', 'en');
        $processedFeedItems = $this->heurekaFeedConfig->processItems($feedItems, $domainConfigMock);

        $generatedXml = $this->getFeedOutputByFeedItems($processedFeedItems, $domainConfigMock);
        $generatedXml = $this->normalizeXml($generatedXml);

        $expectedXml = file_get_contents(__DIR__ . '/Resources/' . self::EXPECTED_XML_FILE_NAME);
        $expectedXml = $this->normalizeXml($expectedXml);

        $this->assertEquals($expectedXml, $generatedXml);
    }

    /**
     * @param \Shopsys\ProductFeed\FeedItemInterface[] $feedItems
     * @param DomainConfigInterface $domainConfig
     * @return string
     */
    private function getFeedOutputByFeedItems($feedItems, $domainConfig)
    {
        $feedContent = '';
        $feedTemplate = $this->twig->load('feed.xml.twig');

        $feedContent .= $feedTemplate->renderBlock('begin', []);

        foreach ($feedItems as $feedItem) {
            $feedContent .= $feedTemplate->renderBlock(
                'item',
                [
                    'item' => $feedItem,
                    'domainConfig' => $domainConfig,
                ]
            );
        }

        $feedContent .= $feedTemplate->renderBlock('end', []);

        return $feedContent;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function getFeedItemsData()
    {
        $feedItems = [];

        $feedItems[] = new TestHeurekaStandardFeedItem(
            self::PRODUCT_ID_FIRST,
            'Product',
            'Lorem ipsum <strong>bold</strong>...',
            'http://www.example.com/product/1',
            'http://www.example.com/product/img/1.jpg',
            '127.90',
            'EUR',
            '79846532EQER',
            5,
            'Best Manufacturer',
            'Electronics | Sub-category',
            ['Param #1' => 'Value #1', 'Param #2' => 'Value #2'],
            '132465798',
            null,
            false,
            self::CATEGORY_ID_FIRST
        );

        $feedItems[] = new TestHeurekaStandardFeedItem(
            self::PRODUCT_ID_SECOND,
            'Product Variant',
            'Lorem ipsum...',
            'http://www.example.com/product/2',
            null,
            '10',
            'EUR',
            null,
            '',
            null,
            null,
            [],
            null,
            12,
            false,
            self::CATEGORY_ID_SECOND
        );

        $feedItems[] = new TestHeurekaStandardFeedItem(
            self::PRODUCT_ID_THIRD,
            'Product with denied selling',
            'Lorem ipsum...',
            'http://www.example.com/product/3',
            'http://www.example.com/product/3.jph',
            '1250',
            'CZK',
            null,
            '',
            null,
            null,
            [],
            null,
            12,
            true,
            self::CATEGORY_ID_SECOND
        );

        return $feedItems;
    }

    /**
     * @param int $domainId
     * @param string $url
     * @param string $locale
     * @return \Shopsys\ProductFeed\DomainConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createDomainConfigMock($domainId, $url, $locale)
    {
        $domainConfigMock = $this->createMock(DomainConfigInterface::class);
        $domainConfigMock->method('getId')->willReturn($domainId);
        $domainConfigMock->method('getUrl')->willReturn($url);
        $domainConfigMock->method('getLocale')->willReturn($locale);

        return $domainConfigMock;
    }

    /**
     * @param $feedContent
     * @return string
     */
    private function normalizeXml($feedContent)
    {
        $document = new DOMDocument('1.0');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $document->loadXML($feedContent);
        $generatedXml = $document->saveXML();

        return $generatedXml;
    }
}

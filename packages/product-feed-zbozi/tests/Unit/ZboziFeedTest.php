<?php

namespace Tests\ProductFeed\ZboziBundle\Unit;

use DOMDocument;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomain;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade;
use Shopsys\ProductFeed\ZboziBundle\ZboziFeedConfig;
use Twig_Environment;
use Twig_Loader_Filesystem;

class ZboziFeedTest extends TestCase
{
    const EXPECTED_XML_FILE_NAME = 'test.xml';
    const PRODUCT_ID_FIRST = 1;
    const PRODUCT_ID_SECOND = 2;
    const PRODUCT_ID_THIRD = 3;
    const PRODUCT_ID_FOURTH = 4;
    const DOMAIN_ID_FIRST = 1;
    const DOMAIN_ID_SECOND = 2;

    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\ZboziFeedConfig
     */
    private $zboziFeedConfig;

    /**
     * @var array
     */
    private $zboziProductDomainsGroupedByDomainId;

    /**
     * @var array
     */
    private $productsIds;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        $this->initTestData();

        $zboziProductDomainFacadeMock = $this->createHeurekaProductDomainFacadeMock();

        $this->zboziFeedConfig = new ZboziFeedConfig($zboziProductDomainFacadeMock);

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
                return $this->zboziProductDomainsGroupedByDomainId[self::DOMAIN_ID_FIRST];
            } elseif ($productsIds === $this->productsIds && $domainId === self::DOMAIN_ID_SECOND) {
                return $this->zboziProductDomainsGroupedByDomainId[self::DOMAIN_ID_SECOND];
            }
            return [];
        };

        /** @var ZboziProductDomainFacade|\PHPUnit\Framework\MockObject\MockObject $googleProductDomainFacadeMock */
        $zboziProductDomainFacadeMock = $this->createMock(ZboziProductDomainFacade::class);

        $zboziProductDomainFacadeMock->method('getZboziProductDomainsByProductsIdsDomainIdIndexedByProductId')
            ->willReturnCallback($returnCallback);

        return $zboziProductDomainFacadeMock;
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function initTestData()
    {
        $this->productsIds = [self::PRODUCT_ID_FIRST, self::PRODUCT_ID_SECOND, self::PRODUCT_ID_THIRD];

        $this->zboziProductDomainsGroupedByDomainId = [
            self::DOMAIN_ID_FIRST => [],
            self::DOMAIN_ID_SECOND => [],
        ];

        $productFirstMock = $this->getMockBuilder(Product::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $productFirstMock->method('getId')->willReturn(self::PRODUCT_ID_FIRST);

        $firstProductMock = $this->createProductMock(self::PRODUCT_ID_FIRST);

        $zboziProductDomainForFirstProductFirstDomainMock = $this->createZboziProductDomainMock(
            $firstProductMock,
            self::DOMAIN_ID_FIRST,
            true,
            7.5,
            8.5
        );

        $this->zboziProductDomainsGroupedByDomainId[self::DOMAIN_ID_FIRST][self::PRODUCT_ID_FIRST] = $zboziProductDomainForFirstProductFirstDomainMock;

        $zboziProductDomainForFirstProductSecondDomainMock = $this->createZboziProductDomainMock(
            $firstProductMock,
            self::DOMAIN_ID_SECOND,
            false,
            null,
            null
        );

        $this->zboziProductDomainsGroupedByDomainId[self::DOMAIN_ID_SECOND][self::PRODUCT_ID_FIRST] = $zboziProductDomainForFirstProductSecondDomainMock;

        $secondProductMock = $this->createProductMock(self::PRODUCT_ID_SECOND);

        $zboziProductDomainForSecondProductFirstDomainMock = $this->createZboziProductDomainMock(
            $secondProductMock,
            self::DOMAIN_ID_FIRST,
            true,
            null,
            null
        );

        $this->zboziProductDomainsGroupedByDomainId[self::DOMAIN_ID_FIRST][self::PRODUCT_ID_SECOND] = $zboziProductDomainForSecondProductFirstDomainMock;

        $zboziProductDomainForSecondProductSecondDomainMock = $this->createZboziProductDomainMock(
            $secondProductMock,
            self::DOMAIN_ID_SECOND,
            false,
            null,
            null
        );

        $this->zboziProductDomainsGroupedByDomainId[self::DOMAIN_ID_SECOND][self::PRODUCT_ID_SECOND] = $zboziProductDomainForSecondProductSecondDomainMock;

        $thirdProductMock = $this->createProductMock(self::PRODUCT_ID_THIRD);

        $zboziProductDomainForThirdProductFirstDomainMock = $this->createZboziProductDomainMock(
            $thirdProductMock,
            self::DOMAIN_ID_FIRST,
            false,
            15.7,
            null
        );

        $this->zboziProductDomainsGroupedByDomainId[self::DOMAIN_ID_FIRST][self::PRODUCT_ID_THIRD] = $zboziProductDomainForThirdProductFirstDomainMock;

        $zboziProductDomainForThirdProductSecondDomainMock = $this->createZboziProductDomainMock(
            $thirdProductMock,
            self::DOMAIN_ID_SECOND,
            false,
            9,
            null
        );

        $this->zboziProductDomainsGroupedByDomainId[self::DOMAIN_ID_SECOND][self::PRODUCT_ID_THIRD] = $zboziProductDomainForThirdProductSecondDomainMock;
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
     * @param bool $show
     * @param null|float $cpc
     * @param null|float $cpcSearch
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createZboziProductDomainMock(MockObject $productMock, $domainId, $show, $cpc, $cpcSearch)
    {
        $zboziProductDomainMock = $this->getMockBuilder(ZboziProductDomain::class)
            ->setMethods(['getProduct', 'getDomainId', 'getShow', 'getCpc', 'getCpcSearch'])
            ->disableOriginalConstructor()
            ->getMock();
        $zboziProductDomainMock->method('getProduct')->willReturn($productMock);
        $zboziProductDomainMock->method('getDomainId')->willReturn($domainId);
        $zboziProductDomainMock->method('getShow')->willReturn($show);
        $zboziProductDomainMock->method('getCpc')->willReturn($cpc);
        $zboziProductDomainMock->method('getCpcSearch')->willReturn($cpcSearch);

        return $zboziProductDomainMock;
    }

    public function testGeneratingOfFeed()
    {
        $feedItems = $this->getFeedItemsData();

        $domainConfigMock = $this->createMock(DomainConfigInterface::class);
        $domainConfigMock->method('getId')->willReturn(1);
        $domainConfigMock->method('getUrl')->willReturn('http://www.example.com/');
        $domainConfigMock->method('getLocale')->willReturn('en');

        $processedFeedItems = $this->zboziFeedConfig->processItems($feedItems, $domainConfigMock);

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

        $feedItems[] = new TestZboziStandardFeedItem(
            self::PRODUCT_ID_FIRST,
            'Product',
            'Lorem ipsum <strong>bold</strong>...',
            'http://www.example.com/product/1',
            'http://www.example.com/product/img/1.jpg',
            '127.90',
            'CZK',
            '79846532EQER',
            5,
            'Best Manufacturer',
            'Electronics | Sub-category',
            ['Param #1' => 'Value #1', 'Param #2' => 'Value #2'],
            '132465798',
            null,
            false
        );

        $feedItems[] = new TestZboziStandardFeedItem(
            self::PRODUCT_ID_SECOND,
            'Product Variant',
            'Lorem ipsum...',
            'http://www.example.com/product/2',
            null,
            '10',
            'CZK',
            null,
            '',
            null,
            null,
            [],
            null,
            12,
            false
        );

        $feedItems[] = new TestZboziStandardFeedItem(
            self::PRODUCT_ID_THIRD,
            'Hidden Product',
            'Lorem ipsum...',
            'http://www.example.com/product/3',
            'http://www.example.com/product/img/3.jpg',
            '256.65789',
            'CZK',
            '6459879887AE',
            '',
            'Manufacturer',
            'Lorem category ipsum...',
            [],
            '132465798',
            null,
            false
        );

        $feedItems[] = new TestZboziStandardFeedItem(
            self::PRODUCT_ID_FOURTH,
            'Product with denied selling',
            'Lorem ipsum...',
            'http://www.example.com/product/4',
            'http://www.example.com/product/img/4.jpg',
            '987.65789',
            'EUR',
            '13E45RT8A',
            '',
            'Manufacturer',
            'Lorem category ipsum...',
            [],
            '132465798',
            null,
            true
        );

        return $feedItems;
    }

    /**
     * @param string $feedContent
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

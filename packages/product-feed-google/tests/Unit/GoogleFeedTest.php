<?php

namespace Tests\ProductFeed\GoogleBundle\Unit;

use DOMDocument;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\GoogleBundle\GoogleFeedConfig;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomain;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainFacade;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

class GoogleFeedTest extends TestCase
{
    const EXPECTED_XML_FILE_NAME = 'test.xml';
    const PRODUCT_ID_FIRST = 1;
    const PRODUCT_ID_SECOND = 2;
    const PRODUCT_ID_THIRD = 3;
    const DOMAIN_ID_FIRST = 1;
    const DOMAIN_ID_SECOND = 2;

    /**
     * @var array
     */
    private $productsIds;

    /**
     * @var array
     */
    private $googleProductDomainsGroupedByDomainId;

    /**
     * @var \Shopsys\ProductFeed\GoogleBundle\GoogleFeedConfig
     */
    private $googleFeedConfig;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        $this->initTestData();

        $googleProductDomainFacadeMock = $this->createGoogleProductDomainFacadeMock();

        /** @var \Symfony\Component\Translation\TranslatorInterface|\PHPUnit\Framework\MockObject\MockObject $translatorMock */
        $translatorMock = $this->createMock(TranslatorInterface::class);

        $this->googleFeedConfig = new GoogleFeedConfig($translatorMock, $googleProductDomainFacadeMock);

        $twigLoader = new Twig_Loader_Filesystem([__DIR__ . '/../../src/Resources/views']);
        $this->twig = new Twig_Environment($twigLoader);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainFacade
     */
    private function createGoogleProductDomainFacadeMock()
    {
        $returnCallback = function ($productsIds, $domainId) {
            if ($productsIds === $this->productsIds && $domainId === self::DOMAIN_ID_FIRST) {
                return $this->googleProductDomainsGroupedByDomainId[self::DOMAIN_ID_FIRST];
            } elseif ($productsIds === $this->productsIds && $domainId === self::DOMAIN_ID_SECOND) {
                return $this->googleProductDomainsGroupedByDomainId[self::DOMAIN_ID_SECOND];
            }
            return [];
        };

        /** @var GoogleProductDomainFacade|\PHPUnit\Framework\MockObject\MockObject $googleProductDomainFacadeMock */
        $googleProductDomainFacadeMock = $this->createMock(GoogleProductDomainFacade::class);

        $googleProductDomainFacadeMock->method('getGoogleProductDomainsByProductsIdsDomainIdIndexedByProductId')
            ->willReturnCallback($returnCallback);

        return $googleProductDomainFacadeMock;
    }

    private function initTestData()
    {
        $this->productsIds = [self::PRODUCT_ID_FIRST, self::PRODUCT_ID_SECOND, self::PRODUCT_ID_THIRD];

        $this->googleProductDomainsGroupedByDomainId = [
            self::DOMAIN_ID_FIRST => [],
            self::DOMAIN_ID_SECOND => [],
        ];

        $firstProductMock = $this->createProductMock(self::PRODUCT_ID_FIRST);
        $googleProductDomainForFirstProductFirstDomainMock = $this->createGoogleProductDomainMock(
            $firstProductMock,
            self::DOMAIN_ID_FIRST,
            true
        );

        $this->googleProductDomainsGroupedByDomainId[self::DOMAIN_ID_FIRST][self::PRODUCT_ID_FIRST] = $googleProductDomainForFirstProductFirstDomainMock;

        $googleProductDomainForFirstProductSecondDomainMock = $this->createGoogleProductDomainMock(
            $firstProductMock,
            self::DOMAIN_ID_SECOND,
            false
        );

        $this->googleProductDomainsGroupedByDomainId[self::DOMAIN_ID_SECOND][self::PRODUCT_ID_FIRST] = $googleProductDomainForFirstProductSecondDomainMock;

        $secondProductMock = $this->createProductMock(self::PRODUCT_ID_SECOND);

        $googleProductDomainForSecondProductFirstDomainMock = $this->createGoogleProductDomainMock(
            $secondProductMock,
            self::DOMAIN_ID_FIRST,
            true
        );

        $this->googleProductDomainsGroupedByDomainId[self::DOMAIN_ID_FIRST][self::PRODUCT_ID_SECOND] = $googleProductDomainForSecondProductFirstDomainMock;

        $googleProductDomainForSecondProductSecondDomainMock = $this->createGoogleProductDomainMock(
            $secondProductMock,
            self::DOMAIN_ID_SECOND,
            false
        );

        $this->googleProductDomainsGroupedByDomainId[self::DOMAIN_ID_SECOND][self::PRODUCT_ID_SECOND] = $googleProductDomainForSecondProductSecondDomainMock;

        $thirdProductMock = $this->createProductMock(self::PRODUCT_ID_THIRD);

        $googleProductDomainForThirdProductFirstDomainMock = $this->createGoogleProductDomainMock(
            $thirdProductMock,
            self::DOMAIN_ID_FIRST,
            false
        );

        $this->googleProductDomainsGroupedByDomainId[self::DOMAIN_ID_FIRST][self::PRODUCT_ID_THIRD] = $googleProductDomainForThirdProductFirstDomainMock;

        $googleProductDomainForThirdProductSecondDomainMock = $this->createGoogleProductDomainMock(
            $thirdProductMock,
            self::DOMAIN_ID_SECOND,
            false
        );

        $this->googleProductDomainsGroupedByDomainId[self::DOMAIN_ID_SECOND][self::PRODUCT_ID_THIRD] = $googleProductDomainForThirdProductSecondDomainMock;
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
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createGoogleProductDomainMock(MockObject $productMock, $domainId, $show)
    {
        $googleProductDomainMock = $this->getMockBuilder(GoogleProductDomain::class)
            ->setMethods(['getProduct', 'getDomainId', 'getShow'])
            ->disableOriginalConstructor()
            ->getMock();
        $googleProductDomainMock->method('getProduct')->willReturn($productMock);
        $googleProductDomainMock->method('getDomainId')->willReturn($domainId);
        $googleProductDomainMock->method('getShow')->willReturn($show);

        return $googleProductDomainMock;
    }

    public function testGeneratingOfFeed()
    {
        $feedItems = $this->getFeedItemsData();

        $domainConfigMock = $this->createMock(DomainConfigInterface::class);
        $domainConfigMock->method('getId')->willReturn(1);
        $domainConfigMock->method('getUrl')->willReturn('http://www.example.com/');
        $domainConfigMock->method('getLocale')->willReturn('en');

        $processedFeedItems = $this->googleFeedConfig->processItems($feedItems, $domainConfigMock);

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

        $feedItems[] = new TestGoogleStandardFeedItem(
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

        $feedItems[] = new TestGoogleStandardFeedItem(
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
            true
        );

        $feedItems[] = new TestGoogleStandardFeedItem(
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

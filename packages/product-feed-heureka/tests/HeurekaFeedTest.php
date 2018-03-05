<?php

namespace Tests;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use Shopsys\Plugin\DataStorageInterface;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider;
use Shopsys\ProductFeed\HeurekaBundle\HeurekaFeedConfig;
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
     * @var array[]
     */
    private $productData;

    /**
     * @var array[]
     */
    private $categoryData;

    /**
     * @var array[]
     */
    private $heurekaCategoryData;

    public function setUp()
    {
        $dataStorageProviderMock = $this->createMock(DataStorageProvider::class);

        $this->productData = [];
        $productDataStorageMock = $this->createMock(DataStorageInterface::class);
        $productDataStorageMock->method('getMultiple')
            ->willReturnCallback(function (array $productIds) {
                return array_intersect_key($this->productData, array_fill_keys($productIds, null));
            });
        $dataStorageProviderMock->method('getProductDataStorage')
            ->willReturn($productDataStorageMock);

        $this->categoryData = [];
        $categoryDataStorageMock = $this->createMock(DataStorageInterface::class);
        $categoryDataStorageMock->method('get')
            ->willReturnCallback(function ($categoryId) {
                return $this->categoryData[$categoryId] ?? null;
            });
        $dataStorageProviderMock->method('getCategoryDataStorage')
            ->willReturn($categoryDataStorageMock);

        $this->heurekaCategoryData = [];
        $heurekaCategoryDataStorageMock = $this->createMock(DataStorageInterface::class);
        $heurekaCategoryDataStorageMock->method('get')
            ->willReturnCallback(function ($heurekaCategoryId) {
                return $this->heurekaCategoryData[$heurekaCategoryId] ?? null;
            });
        $dataStorageProviderMock->method('getHeurekaCategoryDataStorage')
            ->willReturn($heurekaCategoryDataStorageMock);

        $this->heurekaFeedConfig = new HeurekaFeedConfig($dataStorageProviderMock);

        $twigLoader = new Twig_Loader_Filesystem([__DIR__ . '/../src/Resources/views']);
        $this->twig = new Twig_Environment($twigLoader);
    }

    public function testGeneratingOfFeed()
    {
        $this->productData = [
            self::PRODUCT_ID_FIRST => [
                'cpc' => [
                    self::DOMAIN_ID_FIRST => 7.5,
                    self::DOMAIN_ID_SECOND => null,
                ],
            ],
            self::PRODUCT_ID_SECOND => [
                'cpc' => [
                    self::DOMAIN_ID_FIRST => null,
                    self::DOMAIN_ID_SECOND => null,
                ],
            ],
        ];
        $this->categoryData = [
            self::CATEGORY_ID_FIRST => [
                'heureka_category' => self::HEUREKA_CATEGORY_ID_FIRST,
            ],
        ];
        $this->heurekaCategoryData = [
            self::HEUREKA_CATEGORY_ID_FIRST => [
                'id' => self::HEUREKA_CATEGORY_ID_FIRST,
                'name' => 'categoryName',
                'full_name' => 'fullCategoryName',
            ],
        ];

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

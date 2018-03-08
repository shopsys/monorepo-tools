<?php

namespace Tests;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use Shopsys\Plugin\DataStorageInterface;
use Shopsys\Plugin\PluginDataStorageProviderInterface;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\ZboziBundle\ShopsysProductFeedZboziBundle;
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
     * @var \Shopsys\Plugin\DataStorageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $productDataStorageMock;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        $this->productDataStorageMock = $this->createMock(DataStorageInterface::class);
        $pluginDataStorageProviderMock = $this->createMock(PluginDataStorageProviderInterface::class);

        $pluginDataStorageProviderMock->method('getDataStorage')
            ->with(ShopsysProductFeedZboziBundle::class, 'product')
            ->willReturn($this->productDataStorageMock);

        $this->zboziFeedConfig = new ZboziFeedConfig($pluginDataStorageProviderMock);

        $twigLoader = new Twig_Loader_Filesystem([__DIR__ . '/../src/Resources/views']);
        $this->twig = new Twig_Environment($twigLoader);
    }

    public function testGeneratingOfFeed()
    {
        $feedItems = $this->getFeedItemsData();
        $pluginData = $this->getPluginData();

        $this->productDataStorageMock->expects($this->atLeastOnce())
            ->method('getMultiple')
            ->with(array_keys($pluginData))
            ->willReturn($pluginData);

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
     * @return array
     */
    private function getPluginData()
    {
        $pluginData = [];

        $pluginData[self::PRODUCT_ID_FIRST] = [
            'show' => [
                self::DOMAIN_ID_FIRST => true,
                self::DOMAIN_ID_SECOND => false,
            ],
            'cpc' => [
                self::DOMAIN_ID_FIRST => 7.5,
                self::DOMAIN_ID_SECOND => null,
            ],
            'cpc_search' => [
                self::DOMAIN_ID_FIRST => 8.5,
                self::DOMAIN_ID_SECOND => null,
            ],
        ];

        $pluginData[self::PRODUCT_ID_SECOND] = [
            'show' => [
                self::DOMAIN_ID_FIRST => true,
                self::DOMAIN_ID_SECOND => false,
            ],
            'cpc' => [
                self::DOMAIN_ID_FIRST => null,
                self::DOMAIN_ID_SECOND => null,
            ],
            'cpc_search' => [
                self::DOMAIN_ID_FIRST => null,
                self::DOMAIN_ID_SECOND => null,
            ],
        ];

        $pluginData[self::PRODUCT_ID_THIRD] = [
            'show' => [
                self::DOMAIN_ID_FIRST => false,
                self::DOMAIN_ID_SECOND => false,
            ],
            'cpc' => [
                self::DOMAIN_ID_FIRST => 15.7,
                self::DOMAIN_ID_SECOND => null,
            ],
            'cpc_search' => [
                self::DOMAIN_ID_FIRST => 9,
                self::DOMAIN_ID_SECOND => null,
            ],
        ];

        return $pluginData;
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

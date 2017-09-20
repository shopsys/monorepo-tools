<?php

namespace Tests;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use Shopsys\Plugin\DataStorageInterface;
use Shopsys\Plugin\PluginDataStorageProviderInterface;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\HeurekaBundle\HeurekaFeedConfig;
use Shopsys\ProductFeed\HeurekaBundle\ShopsysProductFeedHeurekaBundle;
use Shopsys\ProductFeed\HeurekaCategoryNameProviderInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

class HeurekaFeedTest extends TestCase
{
    const DOMAIN_ID_FIRST = 1;
    const DOMAIN_ID_SECOND = 2;
    const PRODUCT_ID_FIRST = 1;
    const PRODUCT_ID_SECOND = 2;
    const PRODUCT_ID_THIRD = 3;

    const EXPECTED_XML_FILE_NAME = 'test.xml';

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\HeurekaFeedConfig
     */
    private $heurekaFeedConfig;

    /**
     * @var \Shopsys\Plugin\DataStorageInterface|\PHPUnit_Framework_MockObject_MockObject
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
        $heurekaCategoryNameProviderMock = $this->createMock(HeurekaCategoryNameProviderInterface::class);

        $pluginDataStorageProviderMock->method('getDataStorage')
            ->with(ShopsysProductFeedHeurekaBundle::class, 'product')
            ->willReturn($this->productDataStorageMock);
        $heurekaCategoryNameProviderMock->method('getHeurekaCategoryNameForItem')
            ->willReturn('categoryName');

        $this->heurekaFeedConfig = new HeurekaFeedConfig($heurekaCategoryNameProviderMock, $pluginDataStorageProviderMock);

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

        $feedItems[] = new TestStandardFeedItem(
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
            false
        );

        $feedItems[] = new TestStandardFeedItem(
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
            false
        );

        $feedItems[] = new TestStandardFeedItem(
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
            'cpc' => [
               self::DOMAIN_ID_FIRST => 7.5,
               self::DOMAIN_ID_SECOND => null,
            ],
        ];

        $pluginData[self::PRODUCT_ID_SECOND] = [
            'cpc' => [
                self::DOMAIN_ID_FIRST => null,
                self::DOMAIN_ID_SECOND => null,
            ],
        ];

        return $pluginData;
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

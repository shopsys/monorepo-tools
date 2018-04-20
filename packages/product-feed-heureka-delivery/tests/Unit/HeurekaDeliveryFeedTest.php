<?php

namespace Tests\ProductFeed\HeurekaDeliveryBundle\Unit;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\HeurekaDeliveryBundle\HeurekaDeliveryFeedConfig;
use Twig_Environment;
use Twig_Loader_Filesystem;

class HeurekaDeliveryFeedTest extends TestCase
{
    const EXPECTED_XML_FILE_NAME = 'test.xml';
    const PRODUCT_ID_FIRST = 1;
    const PRODUCT_ID_SECOND = 2;
    const PRODUCT_ID_THIRD = 3;

    /**
     * @var \Shopsys\ProductFeed\HeurekaDeliveryBundle\HeurekaDeliveryFeedConfig
     */
    private $heurekaDeliveryFeedConfig;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        $this->heurekaDeliveryFeedConfig = new HeurekaDeliveryFeedConfig();

        $twigLoader = new Twig_Loader_Filesystem([__DIR__ . '/../../src/Resources/views']);
        $this->twig = new Twig_Environment($twigLoader);
    }

    public function testGeneratingOfFeed()
    {
        $feedItems = $this->getFeedItemsData();

        $domainConfigMock = $this->createMock(DomainConfigInterface::class);
        $domainConfigMock->method('getId')->willReturn(1);
        $domainConfigMock->method('getUrl')->willReturn('http://www.example.com/');
        $domainConfigMock->method('getLocale')->willReturn('en');

        $processedFeedItems = $this->heurekaDeliveryFeedConfig->processItems($feedItems, $domainConfigMock);

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
     */
    private function getFeedItemsData()
    {
        $feedItems = [];

        $feedItems[] = new TestDeliveryFeedItem(
            self::PRODUCT_ID_FIRST,
            30
        );

        $feedItems[] = new TestDeliveryFeedItem(
            self::PRODUCT_ID_SECOND,
            0
        );

        $feedItems[] = new TestDeliveryFeedItem(
            self::PRODUCT_ID_THIRD,
            12
        );

        return $feedItems;
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

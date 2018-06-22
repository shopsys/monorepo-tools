<?php

namespace Tests\ProductFeed\HeurekaDeliveryBundle\Unit;

use PHPUnit\Framework\TestCase;
use Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem\HeurekaDeliveryDataMissingException;
use Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem\HeurekaDeliveryFeedItem;
use Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem\HeurekaDeliveryFeedItemFactory;

class HeurekaDeliveryFeedItemTest extends TestCase
{
    /**
     * @var \Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem\HeurekaDeliveryFeedItemFactory
     */
    private $heurekaDeliveryFeedItemFactory;

    public function setUp()
    {
        $this->heurekaDeliveryFeedItemFactory = new HeurekaDeliveryFeedItemFactory();
    }

    public function testHeurekaDeliveryFeedItemIsCreatable()
    {
        $heurekaDeliveryFeedItem = $this->heurekaDeliveryFeedItemFactory->create(['id' => 1, 'stockQuantity' => 5]);

        self::assertInstanceOf(HeurekaDeliveryFeedItem::class, $heurekaDeliveryFeedItem);

        self::assertEquals(1, $heurekaDeliveryFeedItem->getId());
        self::assertEquals(1, $heurekaDeliveryFeedItem->getSeekId());
        self::assertEquals(5, $heurekaDeliveryFeedItem->getStockQuantity());
    }

    public function testHeurekaDeliveryFeedItemIsNotCreatableWhenIdMissing()
    {
        $this->expectException(HeurekaDeliveryDataMissingException::class);

        $this->heurekaDeliveryFeedItemFactory->create(['stockQuantity' => 5]);
    }

    public function testHeurekaDeliveryFeedItemIsNotCreatableWhenStockQuantityMissing()
    {
        $this->expectException(HeurekaDeliveryDataMissingException::class);

        $this->heurekaDeliveryFeedItemFactory->create(['id' => 1]);
    }
}

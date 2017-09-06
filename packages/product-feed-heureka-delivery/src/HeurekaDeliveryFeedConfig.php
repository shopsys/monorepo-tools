<?php

namespace Shopsys\ProductFeed\HeurekaDeliveryBundle;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;

class HeurekaDeliveryFeedConfig implements FeedConfigInterface
{
    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Heureka - delivery';
    }

    /**
     * @return string
     */
    public function getFeedName()
    {
        return 'heureka_delivery';
    }

    /**
     * @return string
     */
    public function getTemplateFilepath()
    {
        return '@ShopsysProductFeedHeurekaDelivery/feed.xml.twig';
    }

    /**
     * @param \Shopsys\ProductFeed\DeliveryFeedItemInterface[] $items
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return \Shopsys\ProductFeed\DeliveryFeedItemInterface[]
     */
    public function processItems(array $items, DomainConfigInterface $domainConfig)
    {
        return $items;
    }
}

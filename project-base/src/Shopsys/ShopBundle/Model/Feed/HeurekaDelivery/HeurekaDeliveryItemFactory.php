<?php

namespace Shopsys\ShopBundle\Model\Feed\HeurekaDelivery;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Model\Feed\FeedItemFactoryInterface;
use Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem;

class HeurekaDeliveryItemFactory implements FeedItemFactoryInterface
{

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem[]
     */
    public function createItems(array $products, DomainConfig $domainConfig) {
        $items = [];
        foreach ($products as $product) {
            $items[] = new HeurekaDeliveryItem(
                $product->getId(),
                $product->getStockQuantity()
            );
        }

        return $items;
    }
}

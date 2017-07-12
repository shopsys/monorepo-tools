<?php

namespace Shopsys\ShopBundle\Model\Feed\HeurekaDelivery;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem;

class HeurekaDeliveryItemFactory
{
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @return \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem[]
     */
    public function createItems(array $products)
    {
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

<?php

namespace Shopsys\ShopBundle\Model\Feed\Delivery;

class DeliveryFeedItemFactory
{
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @return \Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItem[]
     */
    public function createItems(array $products)
    {
        $items = [];
        foreach ($products as $product) {
            $items[] = new DeliveryFeedItem(
                $product->getId(),
                $product->getStockQuantity()
            );
        }

        return $items;
    }
}

<?php

namespace Shopsys\FrameworkBundle\Model\Feed\Delivery;

class DeliveryFeedItemFactory
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     * @return \Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItem[]
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

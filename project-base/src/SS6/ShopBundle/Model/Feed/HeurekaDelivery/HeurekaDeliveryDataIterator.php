<?php

namespace SS6\ShopBundle\Model\Feed\HeurekaDelivery;

use SS6\ShopBundle\Model\Feed\AbstractDataIterator;
use SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem;

class HeurekaDeliveryDataIterator extends AbstractDataIterator {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @return \SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem[]
	 */
	protected function createItems(array $products) {
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

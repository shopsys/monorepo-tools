<?php

namespace SS6\ShopBundle\Model\Feed\HeurekaDelivery;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedItemFactoryInterface;
use SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem;

class HeurekaDeliveryItemFactory implements FeedItemFactoryInterface {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return \SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem[]
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

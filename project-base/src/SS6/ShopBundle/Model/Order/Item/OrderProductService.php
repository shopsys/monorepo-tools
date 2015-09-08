<?php

namespace SS6\ShopBundle\Model\Order\Item;

class OrderProductService {

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
	 */
	public function subtractOrderProductsFromStock(array $orderProducts) {
		foreach ($orderProducts as $orderProduct) {
			$product = $orderProduct->getProduct();
			if ($orderProduct->hasProduct() && $product->isUsingStock()) {
				$originalQuantity = $product->getStockQuantity();
				$product->setStockQuantity($originalQuantity - $orderProduct->getQuantity());
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
	 */
	public function addOrderProductsToStock(array $orderProducts) {
		foreach ($orderProducts as $orderProduct) {
			$product = $orderProduct->getProduct();
			if ($orderProduct->hasProduct() && $product->isUsingStock()) {
				$originalQuantity = $product->getStockQuantity();
				$product->setStockQuantity($originalQuantity + $orderProduct->getQuantity());
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getProductsUsingStockFromOrderProducts(array $orderProducts) {
		$productsUsingStock = [];
		foreach ($orderProducts as $orderProduct) {
			$product = $orderProduct->getProduct();
			if ($orderProduct->hasProduct() && $product->isUsingStock()) {
				$productsUsingStock[] = $product;
			}
		}

		return $productsUsingStock;
	}

}

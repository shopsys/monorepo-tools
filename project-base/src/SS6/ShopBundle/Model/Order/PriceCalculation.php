<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\PriceCalculation as OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderTotalPrice;

class PriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\PriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\PriceCalculation $orderItemPriceCalculation
	 */
	public function __construct(OrderItemPriceCalculation $orderItemPriceCalculation) {
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return \SS6\ShopBundle\Model\Order\OrderTotalPrice
	 */
	public function getOrderTotalPrice(Order $order) {
		$priceWithVat = 0;
		$priceWithoutVat = 0;
		$productPriceWithVat = 0;

		foreach ($order->getItems() as $orderItem) {
			$itemTotalPrice = $this->orderItemPriceCalculation->calculateTotalPrice($orderItem);

			$priceWithVat += $itemTotalPrice->getPriceWithVat();
			$priceWithoutVat += $itemTotalPrice->getPriceWithoutVat();

			if ($orderItem instanceof OrderProduct) {
				$productPriceWithVat += $itemTotalPrice->getPriceWithVat();
			}
		}

		return new OrderTotalPrice($priceWithVat, $priceWithoutVat, $productPriceWithVat);
	}

}

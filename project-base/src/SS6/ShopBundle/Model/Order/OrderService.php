<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderPriceCalculation;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderService {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderPriceCalculation
	 */
	private $orderPriceCalculation;

	public function __construct(
		OrderItemPriceCalculation $orderItemPriceCalculation,
		OrderPriceCalculation $orderPriceCalculation
	) {
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->orderPriceCalculation = $orderPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @return \SS6\ShopBundle\Model\Order\OrderEditResult
	 */
	public function editOrder(Order $order, OrderData $orderData, OrderStatus $orderStatus) {
		$order->edit(
			$orderData,
			$orderStatus
		);

		$orderItemsData = $orderData->items;

		$orderItemsToDelete = [];
		foreach ($order->getItems() as $orderItem) {
			if (array_key_exists($orderItem->getId(), $orderItemsData)) {
				$orderItemData = $orderItemsData[$orderItem->getId()];
				$orderItemData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData);
				$orderItem->edit($orderItemData);
			} else {
				$order->removeItem($orderItem);
				$orderItemsToDelete[] = $orderItem;
			}
		}

		$orderItemsToCreate = [];
		foreach ($orderItemsData as $index => $orderItemData) {
			if (strpos($index, 'new_') === 0) {
				$orderItemData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData);
				$orderItem = new OrderProduct(
					$order,
					$orderItemData->name,
					$orderItemData->priceWithoutVat,
					$orderItemData->priceWithVat,
					$orderItemData->vatPercent,
					$orderItemData->quantity,
					$orderItemData->catnum
				);
				$orderItemsToCreate[] = $orderItem;
			}
		}

		$this->calculateTotalPrice($order);

		return new OrderEditResult($orderItemsToCreate, $orderItemsToDelete);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function calculateTotalPrice(Order $order) {
		$orderTotalPrice = $this->orderPriceCalculation->getOrderTotalPrice($order);
		$order->setTotalPrice($orderTotalPrice);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order[]
	 */
	public function detachCustomer(array $orders) {
		foreach ($orders as $order) {
			/* @var $order \SS6\ShopBundle\Model\Order\Order */
			$order->detachCustomer();
		}
	}

}

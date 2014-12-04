<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
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
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Order\OrderEditResult
	 */
	public function editOrder(Order $order, OrderData $orderData, OrderStatus $orderStatus, User $user = null) {
		$order->edit(
			$orderData,
			$orderStatus,
			$user
		);

		$orderItemsData = $orderData->getItems();

		$orderItemsToDelete = array();
		foreach ($order->getItems() as $orderItem) {
			if (array_key_exists($orderItem->getId(), $orderItemsData)) {
				$orderItemData = $orderItemsData[$orderItem->getId()];
				$orderItemData->setPriceWithoutVat($this->orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData));
				$orderItem->edit($orderItemData);
			} else {
				$order->removeItem($orderItem);
				$orderItemsToDelete[] = $orderItem;
			}
		}

		$orderItemsToCreate = array();
		foreach ($orderItemsData as $index => $orderItemData) {
			if (strpos($index, 'new_') === 0) {
				$orderItemData->setPriceWithoutVat($this->orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData));
				$orderItem = new OrderProduct(
					$order,
					$orderItemData->getName(),
					$orderItemData->getPriceWithoutVat(),
					$orderItemData->getPriceWithVat(),
					$orderItemData->getVatPercent(),
					$orderItemData->getQuantity()
				);
				$orderItemsToCreate[] = $orderItem;
			}
		}

		$this->calculateTotalPrice($order);

		return new OrderEditResult($orderItemsToCreate, $orderItemsToDelete);
	}

	/**
	 * @param Order $order
	 */
	public function calculateTotalPrice(Order $order) {
		$orderTotalPrice = $this->orderPriceCalculation->getOrderTotalPrice($order);
		$order->setTotalPrice($orderTotalPrice);
	}

	/**
	 * @param array \SS6\ShopBundle\Model\Order\Order[]
	 */
	public function detachCustomer(array $orders) {
		foreach ($orders as $order) {
			/* @var $order \SS6\ShopBundle\Model\Order\Order */
			$order->detachCustomer();
		}
	}

}

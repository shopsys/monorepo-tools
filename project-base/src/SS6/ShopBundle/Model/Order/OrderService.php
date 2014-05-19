<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Form\Admin\Order\OrderFormData;
use SS6\ShopBundle\Model\Order\Order;

class OrderService {

	/**
	 *
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Form\Admin\Order\OrderFormData $orderData
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 */
	public function editOrder(Order $order, OrderFormData $orderData, $user) {
		$order->edit(
			$orderData->getFirstName(),
			$orderData->getLastName(),
			$orderData->getEmail(),
			$orderData->getTelephone(),
			$orderData->getStreet(),
			$orderData->getCity(),
			$orderData->getZip(),
			$user,
			$orderData->getCompanyName(),
			$orderData->getCompanyNumber(),
			$orderData->getCompanyTaxNumber(),
			$orderData->getDeliveryFirstName(),
			$orderData->getDeliveryLastName(),
			$orderData->getDeliveryCompanyName(),
			$orderData->getDeliveryTelephone(),
			$orderData->getDeliveryStreet(),
			$orderData->getDeliveryCity(),
			$orderData->getDeliveryZip(),
			$orderData->getNote()
		);

		foreach ($orderData->getItems() as $orderItemData) {
			/* @var $orderItemData \SS6\ShopBundle\Form\Admin\Order\OrderItemFormData */
			$orderItem = $order->getItemById($orderItemData->getId());
			$orderItem->edit(
				$orderItemData->getName(),
				$orderItemData->getPrice(),
				$orderItemData->getQuantity()
			);
		}
		
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

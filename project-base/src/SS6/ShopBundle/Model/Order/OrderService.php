<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Form\Admin\Order\OrderFormData as AdminOrderFormData;
use SS6\ShopBundle\Form\Front\Order\OrderFormData as FrontOrderFormData;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderService {

	/**
	 * @param \SS6\ShopBundle\Form\Front\Order\OrderFormData $orderFormData
	 * @param string $orderNumber
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function createOrder(
		FrontOrderFormData $orderFormData,
		$orderNumber,
		OrderStatus $orderStatus,
		User $user = null
	) {
		$order = new Order(
			$orderNumber,
			$orderFormData->getTransport(),
			$orderFormData->getPayment(),
			$orderStatus,
			$orderFormData->getFirstName(),
			$orderFormData->getLastName(),
			$orderFormData->getEmail(),
			$orderFormData->getTelephone(),
			$orderFormData->getStreet(),
			$orderFormData->getCity(),
			$orderFormData->getPostcode(),
			$user,
			$orderFormData->getNote()
		);

		if ($orderFormData->isCompanyCustomer()) {
			$order->setCompanyInfo(
				$orderFormData->getCompanyName(),
				$orderFormData->getCompanyNumber(),
				$orderFormData->getCompanyTaxNumber()
			);
		}

		if ($orderFormData->isDeliveryAddressFilled()) {
			$order->setDeliveryAddress(
				$orderFormData->getDeliveryFirstName(),
				$orderFormData->getDeliveryLastName(),
				$orderFormData->getDeliveryCompanyName(),
				$orderFormData->getDeliveryTelephone(),
				$orderFormData->getDeliveryStreet(),
				$orderFormData->getDeliveryCity(),
				$orderFormData->getDeliveryPostcode()
			);
		}

		return $order;
	}

	/**
	 *
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Form\Admin\Order\OrderFormData $orderData
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 */
	public function editOrder(Order $order, AdminOrderFormData $orderData, OrderStatus $orderStatus, User $user = null) {
		$order->edit(
			$orderStatus,
			$orderData->getFirstName(),
			$orderData->getLastName(),
			$orderData->getEmail(),
			$orderData->getTelephone(),
			$orderData->getStreet(),
			$orderData->getCity(),
			$orderData->getPostcode(),
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
			$orderData->getDeliveryPostcode(),
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

		$order->recalcTotalPrices();
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

	/**
	 * @param \SS6\ShopBundle\Form\Front\Order\OrderFormData $orderFormData
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function prefillFrontFormData(FrontOrderFormData $orderFormData, User $user, Order $order = null) {
		if ($order instanceof Order) {
			$this->prefillFrontFormDataFromOrder($orderFormData, $order);
		} else {
			$this->prefillFrontFormDataFromCustomer($orderFormData, $user);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Form\Front\Order\OrderFormData $orderFormData
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	private function prefillFrontFormDataFromOrder(FrontOrderFormData $orderFormData, Order $order) {
		$orderFormData->setTransport($order->getTransport());
		$orderFormData->setPayment($order->getPayment());
		$orderFormData->setFirstName($order->getFirstName());
		$orderFormData->setLastName($order->getLastName());
		$orderFormData->setEmail($order->getEmail());
		$orderFormData->setTelephone($order->getTelephone());

		if ($orderFormData->getCompanyName() !== null
				|| $orderFormData->getCompanyNumber() !== null
				|| $orderFormData->getCompanyTaxNumber() !== null) {
			$orderFormData->setCompanyCustomer(true);
		} else {
			$orderFormData->setCompanyCustomer(false);
		}

		$orderFormData->setCompanyName($order->getCompanyName());
		$orderFormData->setCompanyNumber($order->getCompanyNumber());
		$orderFormData->setCompanyTaxNumber($order->getCompanyTaxNumber());
		$orderFormData->setStreet($order->getStreet());
		$orderFormData->setCity($order->getCity());
		$orderFormData->setPostcode($order->getPostcode());

		if ($orderFormData->getDeliveryFirstName() !== null
				|| $orderFormData->getDeliveryLastName() !== null
				|| $orderFormData->getDeliveryCompanyName() !== null
				|| $orderFormData->getDeliveryTelephone() !== null
				|| $orderFormData->getDeliveryStreet() !== null
				|| $orderFormData->getDeliveryCity() !== null
				|| $orderFormData->getDeliveryPostcode() !== null) {
			$orderFormData->setDeliveryAddressFilled(true);
		} else {
			$orderFormData->setDeliveryAddressFilled(false);
		}

		$orderFormData->setDeliveryFirstName($order->getDeliveryFirstName());
		$orderFormData->setDeliveryLastName($order->getDeliveryLastName());
		$orderFormData->setDeliveryCompanyName($order->getDeliveryCompanyName());
		$orderFormData->setDeliveryTelephone($order->getDeliveryTelephone());
		$orderFormData->setDeliveryStreet($order->getDeliveryStreet());
		$orderFormData->setDeliveryCity($order->getDeliveryCity());
		$orderFormData->setDeliveryPostcode($order->getDeliveryPostcode());
	}

	/**
	 * @param \SS6\ShopBundle\Form\Front\Order\OrderFormData $orderFormData
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	private function prefillFrontFormDataFromCustomer(FrontOrderFormData $orderFormData, User $user) {
		$orderFormData->setFirstName($user->getFirstName());
		$orderFormData->setLastName($user->getLastName());
		$orderFormData->setEmail($user->getEmail());
		$orderFormData->setTelephone($user->getBillingAddress()->getTelephone());
		$orderFormData->setCompanyCustomer($user->getBillingAddress()->isCompanyCustomer());
		$orderFormData->setCompanyName($user->getBillingAddress()->getCompanyName());
		$orderFormData->setCompanyNumber($user->getBillingAddress()->getCompanyNumber());
		$orderFormData->setCompanyTaxNumber($user->getBillingAddress()->getCompanyTaxNumber());
		$orderFormData->setStreet($user->getBillingAddress()->getStreet());
		$orderFormData->setCity($user->getBillingAddress()->getCity());
		$orderFormData->setPostcode($user->getBillingAddress()->getPostcode());
		if ($user->getDeliveryAddress() !== null) {
			$orderFormData->setDeliveryAddressFilled(true);
			// firstName + lastName ?
			$orderFormData->setDeliveryCompanyName($user->getDeliveryAddress()->getCompanyName());
			$orderFormData->setDeliveryTelephone($user->getDeliveryAddress()->getTelephone());
			$orderFormData->setDeliveryStreet($user->getDeliveryAddress()->getStreet());
			$orderFormData->setDeliveryCity($user->getDeliveryAddress()->getCity());
			$orderFormData->setDeliveryPostcode($user->getDeliveryAddress()->getPostcode());
		} else {
			$orderFormData->setDeliveryAddressFilled(false);
		}
	}

}

<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Item\PriceCalculation as OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\PriceCalculation as OrderPriceCalculation;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderService {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\PriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Order\PriceCalculation
	 */
	private $orderPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\PriceCalculation $orderItemPriceCalculation
	 * @param \SS6\ShopBundle\Model\Order\PriceCalculation $orderPriceCalculation
	 */
	public function __construct(
		OrderItemPriceCalculation $orderItemPriceCalculation,
		OrderPriceCalculation $orderPriceCalculation
	) {
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->orderPriceCalculation = $orderPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param string $orderNumber
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function createOrder(
		OrderData $orderData,
		$orderNumber,
		OrderStatus $orderStatus,
		User $user = null
	) {
		$order = new Order(
			$orderData,
			$orderNumber,
			$orderStatus,
			$user
		);
		return $order;
	}

	/**
	 *
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 */
	public function editOrder(Order $order, OrderData $orderData, OrderStatus $orderStatus, User $user = null) {
		$order->edit(
			$orderData,
			$orderStatus,
			$user
		);

		foreach ($orderData->getItems() as $orderItemData) {
			/* @var $orderItemData \SS6\ShopBundle\Model\Order\OrderItemData */
			$this->orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData);
			$orderItem = $order->getItemById($orderItemData->getId());
			$orderItem->edit($orderItemData);
		}

		$orderTotalPrice = $this->orderPriceCalculation->getOrderTotalPrice($order);
		$order->setTotalPrice($orderTotalPrice);
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

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function prefillFrontFormData(OrderData $orderData, User $user, Order $order = null) {
		if ($order instanceof Order) {
			$this->prefillTransportAndPaymentFromOrder($orderData, $order);
		}
		$this->prefillFrontFormDataFromCustomer($orderData, $user);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	private function prefillTransportAndPaymentFromOrder(OrderData $orderData, Order $order) {
		$orderData->setTransport($order->getTransport());
		$orderData->setPayment($order->getPayment());
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	private function prefillFrontFormDataFromCustomer(OrderData $orderData, User $user) {
		$orderData->setFirstName($user->getFirstName());
		$orderData->setLastName($user->getLastName());
		$orderData->setEmail($user->getEmail());
		$orderData->setTelephone($user->getBillingAddress()->getTelephone());
		$orderData->setCompanyCustomer($user->getBillingAddress()->isCompanyCustomer());
		$orderData->setCompanyName($user->getBillingAddress()->getCompanyName());
		$orderData->setCompanyNumber($user->getBillingAddress()->getCompanyNumber());
		$orderData->setCompanyTaxNumber($user->getBillingAddress()->getCompanyTaxNumber());
		$orderData->setStreet($user->getBillingAddress()->getStreet());
		$orderData->setCity($user->getBillingAddress()->getCity());
		$orderData->setPostcode($user->getBillingAddress()->getPostcode());
		if ($user->getDeliveryAddress() !== null) {
			$orderData->setDeliveryAddressFilled(true);
			$orderData->setDeliveryContactPerson($user->getDeliveryAddress()->getContactPerson());
			$orderData->setDeliveryCompanyName($user->getDeliveryAddress()->getCompanyName());
			$orderData->setDeliveryTelephone($user->getDeliveryAddress()->getTelephone());
			$orderData->setDeliveryStreet($user->getDeliveryAddress()->getStreet());
			$orderData->setDeliveryCity($user->getDeliveryAddress()->getCity());
			$orderData->setDeliveryPostcode($user->getDeliveryAddress()->getPostcode());
		} else {
			$orderData->setDeliveryAddressFilled(false);
		}
	}

}

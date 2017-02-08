<?php

namespace SS6\ShopBundle\Model\Order\Item;

use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;

class OrderPaymentData extends OrderItemData {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 */
	public $payment;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem $orderPayment
	 */
	public function setFromEntity(OrderItem $orderPayment) {
		if ($orderPayment instanceof OrderPayment) {
			$this->payment = $orderPayment->getPayment();
			parent::setFromEntity($orderPayment);
		} else {
			throw new \SS6\ShopBundle\Model\Order\Item\Exception\InvalidArgumentException(
				'Instance of ' . OrderPayment::class . ' is required as argument.'
			);
		}
	}

}

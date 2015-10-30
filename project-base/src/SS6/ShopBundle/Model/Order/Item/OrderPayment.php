<?php

namespace SS6\ShopBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\Item\OrderPaymentData;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Price;

/**
 * @ORM\Entity
 */
class OrderPayment extends OrderItem {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Payment\Payment")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $payment;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param string $name
	 * @param \SS6\ShopBundle\Model\Pricing\Price $price
	 * @param string $vatPercent
	 * @param int $quantity
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 */
	public function __construct(
		Order $order,
		$name,
		Price $price,
		$vatPercent,
		$quantity,
		Payment $payment
	) {
		parent::__construct(
			$order,
			$name,
			$price,
			$vatPercent,
			$quantity,
			null,
			null
		);
		$this->payment = $payment;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItemData $orderPaymentData
	 */
	public function edit(OrderItemData $orderPaymentData) {
		if ($orderPaymentData instanceof OrderPaymentData) {
			$this->payment = $orderPaymentData->payment;
			parent::edit($orderPaymentData);
		} else {
			throw new \SS6\ShopBundle\Model\Order\Item\Exception\InvalidArgumentException(
				'Instance of ' . OrderPaymentData::class . ' is required as argument.'
			);
		}
	}

}

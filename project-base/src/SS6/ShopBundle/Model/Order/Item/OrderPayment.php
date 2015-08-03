<?php

namespace SS6\ShopBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Payment\Payment;

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
	 * @param string $priceWithoutVat
	 * @param string $priceWithVat
	 * @param string $vatPercent
	 * @param int $quantity
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 */
	public function __construct(
		Order $order,
		$name,
		$priceWithoutVat,
		$priceWithVat,
		$vatPercent,
		$quantity,
		Payment $payment
	) {
		parent::__construct(
			$order,
			$name,
			$priceWithoutVat,
			$priceWithVat,
			$vatPercent,
			$quantity,
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

}

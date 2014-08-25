<?php

namespace SS6\ShopBundle\Model\Payment\Detail;

use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Payment\Payment;

class Detail {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 */
	private $payment;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $price;

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Price $price
	 */
	public function __construct(Payment $payment, Price $price) {
		$this->payment = $payment;
		$this->price = $price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getPrice() {
		return $this->price;
	}

}

<?php

namespace SS6\ShopBundle\Model\Payment\Detail;

use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Price;

class PaymentDetail {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 */
	private $payment;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $basePrice;

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Price $basePrice
	 */
	public function __construct(Payment $payment, Price $basePrice) {
		$this->payment = $payment;
		$this->basePrice = $basePrice;
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
	public function getBasePrice() {
		return $this->basePrice;
	}

}

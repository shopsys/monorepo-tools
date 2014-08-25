<?php

namespace SS6\ShopBundle\Model\Payment\Detail;

use SS6\ShopBundle\Model\Payment\PriceCalculation;
use SS6\ShopBundle\Model\Payment\Payment;

class Factory {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PriceCalculation
	 */
	private $priceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Payment\PriceCalculation $priceCalculation
	 */
	public function __construct(PriceCalculation $priceCalculation) {
		$this->priceCalculation = $priceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @return \SS6\ShopBundle\Model\Payment\Detail\Detail
	 */
	public function createDetailForPayment(Payment $payment) {
		return new Detail(
			$payment,
			$this->getPrice($payment)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @return \SS6\ShopBundle\Model\Payment\Detail\Detail[]
	 */
	public function createDetailsForPayments(array $payments) {
		$details = array();

		foreach ($payments as $payment) {
			$details[] = $this->createDetailForPayment($payment);
		}

		return $details;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getPrice(Payment $payment) {
		return $this->priceCalculation->calculatePrice($payment);
	}

}

<?php

namespace SS6\ShopBundle\Model\Payment;

use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;

class PaymentPriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\BasePriceCalculation
	 */
	private $basePriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
	 * @param \SS6\ShopBundle\Model\Pricing\PricingSetting $pricingSetting
	 */
	public function __construct(
		BasePriceCalculation $basePriceCalculation,
		PricingSetting $pricingSetting
	) {
		$this->pricingSetting = $pricingSetting;
		$this->basePriceCalculation = $basePriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePrice(Payment $payment) {
		return $this->basePriceCalculation->calculatePrice(
			$payment->getPrice(),
			$this->pricingSetting->getInputPriceType(),
			$payment->getVat()
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @return \SS6\ShopBundle\Model\Pricing\Price[]
	 */
	public function calculatePricesById(array $payments) {
		$paymentsPrices = array();
		foreach ($payments as $payment) {
			$paymentsPrices[$payment->getId()] = $this->calculatePrice($payment);
		}

		return $paymentsPrices;
	}

}

<?php

namespace SS6\ShopBundle\Model\Payment;

use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Price;
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
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param \SS6\ShopBundle\Model\Pricing\Price $productsPrice
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePrice(
		Payment $payment,
		Currency $currency,
		Price $productsPrice,
		$domainId
	) {
		if ($this->isFree($productsPrice, $domainId)) {
			return new Price(0, 0);
		}

		return $this->calculateIndependentPrice($payment, $currency);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculateIndependentPrice(
		Payment $payment,
		Currency $currency
	) {
		return $this->basePriceCalculation->calculateBasePrice(
			$payment->getPrice($currency)->getPrice(),
			$this->pricingSetting->getInputPriceType(),
			$payment->getVat()
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Price $productsPrice
	 * @param int $domainId
	 * @return bool
	 */
	private function isFree(Price $productsPrice, $domainId) {
		$freeTransportAndPaymentPriceLimit = $this->pricingSetting->getFreeTransportAndPaymentPriceLimit($domainId);

		if ($freeTransportAndPaymentPriceLimit === null) {
			return false;
		}

		return $productsPrice->getPriceWithVat() >= $freeTransportAndPaymentPriceLimit;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return \SS6\ShopBundle\Model\Pricing\Price[paymentId]
	 */
	public function calculatePricesById(
		array $payments,
		Currency $currency,
		Price $productsPrice,
		$domainId
	) {
		$paymentsPrices = [];
		foreach ($payments as $payment) {
			$paymentsPrices[$payment->getId()] = $this->calculatePrice(
				$payment,
				$currency,
				$productsPrice,
				$domainId
			);
		}

		return $paymentsPrices;
	}

}

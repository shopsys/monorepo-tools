<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Pricing\PricingSetting;

class TransportPriceCalculation {

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
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param \SS6\ShopBundle\Model\Pricing\Price $productsPrice
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePrice(
		Transport $transport,
		Currency $currency,
		Price $productsPrice,
		$domainId
	) {
		if ($this->isFree($productsPrice, $domainId)) {
			return new Price(0, 0);
		}

		return $this->calculateIndependentPrice($transport, $currency);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculateIndependentPrice(
		Transport $transport,
		Currency $currency
	) {
		return $this->basePriceCalculation->calculateBasePrice(
			$transport->getPrice($currency)->getPrice(),
			$this->pricingSetting->getInputPriceType(),
			$transport->getVat()
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
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param \SS6\ShopBundle\Model\Pricing\Price $productsPrice
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Pricing\Price[transportId]
	 */
	public function calculatePricesById(
		array $transports,
		Currency $currency,
		Price $productsPrice,
		$domainId
	) {
		$transportsPrices = [];
		foreach ($transports as $transport) {
			$transportsPrices[$transport->getId()] = $this->calculatePrice(
				$transport,
				$currency,
				$productsPrice,
				$domainId
			);
		}

		return $transportsPrices;
	}

}

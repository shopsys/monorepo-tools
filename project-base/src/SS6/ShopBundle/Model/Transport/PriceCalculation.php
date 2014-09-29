<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;

class PriceCalculation {

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
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePrice(Transport $transport) {
		return $this->basePriceCalculation->calculatePrice(
			$transport->getPrice(),
			$this->pricingSetting->getInputPriceType(),
			$transport->getVat()
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @return \SS6\ShopBundle\Model\Pricing\Price[] array indices are preserved
	 */
	public function calculatePrices(array $transports) {
		$transportsPrices = array();
		foreach ($transports as $key => $transport) {
			$transportsPrices[$key] = $this->calculatePrice($transport);
		}

		return $transportsPrices;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @return \SS6\ShopBundle\Model\Pricing\Price[]
	 */
	public function calculatePricesById(array $transports) {
		$transportsPrices = array();
		foreach ($transports as $transport) {
			$transportsPrices[$transport->getId()] = $this->calculatePrice($transport);
		}

		return $transportsPrices;
	}

}

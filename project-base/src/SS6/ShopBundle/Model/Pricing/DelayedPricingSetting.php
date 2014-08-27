<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Pricing\InputPriceFacade;

class DelayedPricingSetting {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\InputPriceFacade
	 */
	private $inputPriceFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\PricingSetting $pricingSetting
	 * @param \SS6\ShopBundle\Model\Pricing\InputPriceFacade $inputPriceFacade
	 */
	public function __construct(PricingSetting $pricingSetting, InputPriceFacade $inputPriceFacade) {
		$this->pricingSetting = $pricingSetting;
		$this->inputPriceFacade = $inputPriceFacade;
	}

	/**
	 * @param int $inputPriceType
	 * @throws \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException
	 */
	public function setInputPriceType($inputPriceType) {
		if (!in_array($inputPriceType, array_keys($this->pricingSetting->getInputPriceTypes()))) {
			throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
		}

		$currentInputPriceType = $this->pricingSetting->getInputPriceType();

		if ($currentInputPriceType != $inputPriceType) {
			switch ($inputPriceType) {
				case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
					$this->inputPriceFacade->scheduleSetInputPricesWithoutVat();
					break;

				case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
					$this->inputPriceFacade->scheduleSetInputPricesWithVat();
					break;
			}
		}
	}

}

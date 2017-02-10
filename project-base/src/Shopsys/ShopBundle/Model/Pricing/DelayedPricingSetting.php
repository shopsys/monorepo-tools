<?php

namespace Shopsys\ShopBundle\Model\Pricing;

use Shopsys\ShopBundle\Model\Pricing\InputPriceRecalculationScheduler;

class DelayedPricingSetting {

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\InputPriceRecalculationScheduler
	 */
	private $inputPriceRecalculationScheduler;

	public function __construct(
		PricingSetting $pricingSetting,
		InputPriceRecalculationScheduler $inputPriceRecalculationScheduler
	) {
		$this->pricingSetting = $pricingSetting;
		$this->inputPriceRecalculationScheduler = $inputPriceRecalculationScheduler;
	}

	/**
	 * @param int $inputPriceType
	 */
	public function scheduleSetInputPriceType($inputPriceType) {
		if (!in_array($inputPriceType, $this->pricingSetting->getInputPriceTypes())) {
			throw new \Shopsys\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException('Unknow input price type');
		}

		$currentInputPriceType = $this->pricingSetting->getInputPriceType();

		if ($currentInputPriceType != $inputPriceType) {
			switch ($inputPriceType) {
				case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
					$this->inputPriceRecalculationScheduler->scheduleSetInputPricesWithoutVat();
					break;

				case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
					$this->inputPriceRecalculationScheduler->scheduleSetInputPricesWithVat();
					break;
			}
		}
	}

}

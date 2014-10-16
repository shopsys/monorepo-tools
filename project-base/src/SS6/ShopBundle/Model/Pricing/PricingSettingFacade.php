<?php

namespace SS6\ShopBundle\Model\Pricing;

class PricingSettingFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\DelayedPricingSetting
	 */
	private $delayedPricingSetting;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\PricingSetting $pricingSetting
	 * @param \SS6\ShopBundle\Model\Pricing\DelayedPricingSetting $delayedPricingSetting
	 */
	public function __construct(
		PricingSetting $pricingSetting,
		DelayedPricingSetting $delayedPricingSetting
	) {
		$this->pricingSetting = $pricingSetting;
		$this->delayedPricingSetting = $delayedPricingSetting;
	}

	/**
	 * @param int $inputPriceType
	 */
	public function setInputPriceType($inputPriceType) {
		$this->delayedPricingSetting->setInputPriceType($inputPriceType);
	}

	/**
	 * @param int $roundingType
	 */
	public function setRoundingType($roundingType) {
		$this->pricingSetting->setRoundingType($roundingType);
	}

}

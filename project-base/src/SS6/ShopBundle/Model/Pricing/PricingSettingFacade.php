<?php

namespace SS6\ShopBundle\Model\Pricing;

class PricingSettingFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\DelayedPricingSetting
	 */
	private $delayedPricingSetting;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\DelayedPricingSetting $delayedPricingSetting
	 */
	public function __construct(
		DelayedPricingSetting $delayedPricingSetting
	) {
		$this->delayedPricingSetting = $delayedPricingSetting;
	}

	/**
	 * @param int $inputPriceType
	 */
	public function setInputPriceType($inputPriceType) {
		$this->delayedPricingSetting->setInputPriceType($inputPriceType);
	}

}

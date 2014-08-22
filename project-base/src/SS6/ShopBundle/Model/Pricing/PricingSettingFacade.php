<?php

namespace SS6\ShopBundle\Model\Pricing;

class PricingSettingFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\PricingSetting $pricingSetting
	 */
	public function __construct(PricingSetting $pricingSetting) {
		$this->pricingSetting = $pricingSetting;
	}

	/**
	 * @param array $pricingSettingData
	 */
	public function edit(array $pricingSettingData) {
		$this->pricingSetting->scheduleSetInputPriceType($pricingSettingData['type']);
	}

}

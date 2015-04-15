<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;

class UserDataFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade $pricingGroupSettingFacade
	 */
	public function __construct(PricingGroupSettingFacade $pricingGroupSettingFacade) {
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
	}

	/**
	 * @param int $domainId
	 */
	public function createDefault($domainId) {
		$userData = new UserData();
		$userData->pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainId);

		return $userData;
	}
}

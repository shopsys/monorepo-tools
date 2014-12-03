<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;

class UserDataFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade
	 */
	public function __construct(PricingGroupFacade $pricingGroupFacade) {
		$this->pricingGroupFacade = $pricingGroupFacade;
	}

	/**
	 * @param int $domainId
	 */
	public function createDefault($domainId) {
		$userData = new UserData();
		$userData->setPricingGroup($this->pricingGroupFacade->getDefaultPricingGroupByDomainId($domainId));

		return $userData;
	}
}

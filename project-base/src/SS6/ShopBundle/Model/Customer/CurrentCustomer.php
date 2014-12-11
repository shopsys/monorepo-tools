<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CurrentCustomer {

	/**
	 * @var \Symfony\Component\Security\Core\SecurityContextInterface
	 */
	private $securityContext;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	public function __construct(
		SecurityContextInterface $securityContext,
		PricingGroupFacade $pricingGroupFacade
	) {
		$this->securityContext = $securityContext;
		$this->pricingGroupFacade = $pricingGroupFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	public function getPricingGroup() {
		$user = $this->getCurrentUser();
		if ($user === null) {
			return $this->pricingGroupFacade->getDefaultPricingGroupByCurrentDomain();
		} else {
			return $user->getPricingGroup();
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	private function getCurrentUser() {
		$token = $this->securityContext->getToken();
		if ($token === null) {
			return null;
		}

		$user = $token->getUser();
		if (!$user instanceof User) {
			return null;
		}

		return $user;
	}
}

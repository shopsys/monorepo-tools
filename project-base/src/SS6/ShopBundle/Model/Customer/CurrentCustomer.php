<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CurrentCustomer {

	/**
	 * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
	 */
	private $tokenStorage;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	public function __construct(
		TokenStorage $tokenStorage,
		PricingGroupSettingFacade $ricingGroupSettingFacade
	) {
		$this->tokenStorage = $tokenStorage;
		$this->pricingGroupSettingFacade = $ricingGroupSettingFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	public function getPricingGroup() {
		$user = $this->findCurrentUser();
		if ($user === null) {
			return $this->pricingGroupSettingFacade->getDefaultPricingGroupByCurrentDomain();
		} else {
			return $user->getPricingGroup();
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\User|null
	 */
	public function findCurrentUser() {
		$token = $this->tokenStorage->getToken();
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

<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Form\Admin\Customer\CustomerFormType;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository;

class CustomerFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository
	 */
	private $pricingGroupRepository;

	public function __construct(
		Domain $domain,
		SelectedDomain $selectedDomain,
		PricingGroupRepository $pricingGroupRepository
	) {
		$this->domain = $domain;
		$this->selectedDomain = $selectedDomain;
		$this->pricingGroupRepository = $pricingGroupRepository;
	}

	/**
	 * @param string $scenario
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return \SS6\ShopBundle\Form\Admin\Customer\CustomerFormType
	 */
	public function create($scenario, User $user = null) {
		$allDomains = $this->domain->getAll();
		if ($scenario === CustomerFormType::SCENARIO_EDIT) {
			$allPricingGroups = $this->pricingGroupRepository->getPricingGroupsByDomainId($user->getDomainId());
		} else {
			$allPricingGroups = $this->pricingGroupRepository->getAll();
		}

		return new CustomerFormType($scenario, $allDomains, $this->selectedDomain, $allPricingGroups);
	}

}

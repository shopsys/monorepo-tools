<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Form\Admin\Customer\CustomerFormType;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository;

class CustomerFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository
	 */
	private $pricingGroupRepository;

	public function __construct(
		Domain $domain,
		PricingGroupRepository $pricingGroupRepository
	) {
		$this->domain = $domain;
		$this->pricingGroupRepository = $pricingGroupRepository;
	}

	/**
	 * @param string $scenario
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain $selectedDomain
	 * @return \SS6\ShopBundle\Form\Admin\Customer\CustomerFormType
	 */
	public function create($scenario, SelectedDomain $selectedDomain = null) {
		$allDomains = $this->domain->getAll();
		if ($selectedDomain === null) {
			$allPricingGroups = $this->pricingGroupRepository->getAll();
		} else {
			$allPricingGroups = $this->pricingGroupRepository->getPricingGroupsByDomainId($selectedDomain->getId());
		}

		return new CustomerFormType($scenario, $allDomains, $selectedDomain, $allPricingGroups);
	}

}

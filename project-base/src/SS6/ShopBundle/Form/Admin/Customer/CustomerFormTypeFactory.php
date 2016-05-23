<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Form\Admin\Customer\CustomerFormType;
use SS6\ShopBundle\Model\Country\CountryFacade;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository;

class CustomerFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository
	 */
	private $pricingGroupRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Country\CountryFacade
	 */
	private $countryFacade;

	public function __construct(
		SelectedDomain $selectedDomain,
		PricingGroupRepository $pricingGroupRepository,
		CountryFacade $countryFacade
	) {
		$this->selectedDomain = $selectedDomain;
		$this->pricingGroupRepository = $pricingGroupRepository;
		$this->countryFacade = $countryFacade;
	}

	/**
	 * @param string $scenario
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return \SS6\ShopBundle\Form\Admin\Customer\CustomerFormType
	 */
	public function create($scenario, User $user = null) {
		if ($scenario === CustomerFormType::SCENARIO_EDIT) {
			$allPricingGroups = $this->pricingGroupRepository->getPricingGroupsByDomainId($user->getDomainId());
		} else {
			$allPricingGroups = $this->pricingGroupRepository->getAll();
		}

		$countries = $this->countryFacade->getAllByDomainId($this->selectedDomain->getId());

		return new CustomerFormType($scenario, $countries, $this->selectedDomain, $allPricingGroups);
	}

}

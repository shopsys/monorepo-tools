<?php

namespace Shopsys\ShopBundle\Form\Admin\Customer;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Form\Admin\Customer\CustomerFormType;
use Shopsys\ShopBundle\Model\Country\CountryFacade;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupRepository;

class CustomerFormTypeFactory
{

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupRepository
     */
    private $pricingGroupRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Country\CountryFacade
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
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return \Shopsys\ShopBundle\Form\Admin\Customer\CustomerFormType
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

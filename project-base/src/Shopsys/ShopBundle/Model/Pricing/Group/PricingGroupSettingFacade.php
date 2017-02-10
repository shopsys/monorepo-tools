<?php

namespace Shopsys\ShopBundle\Model\Pricing\Group;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupRepository;

class PricingGroupSettingFacade
{
    public function __construct(
        PricingGroupRepository $pricingGroupRepository,
        Domain $domain,
        SelectedDomain $selectedDomain,
        Setting $setting
    ) {
        $this->pricingGroupRepository = $pricingGroupRepository;
        $this->domain = $domain;
        $this->selectedDomain = $selectedDomain;
        $this->setting = $setting;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[]
     */
    public function getPricingGroupsBySelectedDomainId() {
        return $this->pricingGroupRepository->getPricingGroupsByDomainId($this->selectedDomain->getId());
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return bool
     */
    public function isPricingGroupUsed(PricingGroup $pricingGroup) {
        return $this->pricingGroupRepository->existsUserWithPricingGroup($pricingGroup)
            || $this->isPricingGroupDefault($pricingGroup);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup
     */
    public function getDefaultPricingGroupByDomainId($domainId) {
        $defaultPricingGroupId = $this->setting->getForDomain(Setting::DEFAULT_PRICING_GROUP, $domainId);

        return $this->pricingGroupRepository->getById($defaultPricingGroupId);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup
     */
    public function getDefaultPricingGroupByCurrentDomain() {
        return $this->getDefaultPricingGroupByDomainId($this->domain->getId());
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup
     */
    public function getDefaultPricingGroupBySelectedDomain() {
        return $this->getDefaultPricingGroupByDomainId($this->selectedDomain->getId());
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     */
    public function setDefaultPricingGroup(PricingGroup $pricingGroup) {
        $this->setting->setForDomain(Setting::DEFAULT_PRICING_GROUP, $pricingGroup->getId(), $this->selectedDomain->getId());
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return bool
     */
    public function isPricingGroupDefault(PricingGroup $pricingGroup) {
        return $pricingGroup === $this->getDefaultPricingGroupBySelectedDomain();
    }
}

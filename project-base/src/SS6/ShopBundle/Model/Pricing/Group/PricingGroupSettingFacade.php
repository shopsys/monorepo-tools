<?php

namespace SS6\ShopBundle\Model\Pricing\Group;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository;
use SS6\ShopBundle\Model\Setting\Setting;

class PricingGroupSettingFacade {

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
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	public function getPricingGroupsBySelectedDomainId() {
		return $this->pricingGroupRepository->getPricingGroupsByDomainId($this->selectedDomain->getId());
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return bool
	 */
	public function isPricingGroupUsed(PricingGroup $pricingGroup) {
		return $this->pricingGroupRepository->existsUserWithPricingGroup($pricingGroup)
			|| $this->isPricingGroupDefault($pricingGroup);
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Pricing\PricingGroup
	 */
	public function getDefaultPricingGroupByDomainId($domainId) {
		$defaultPricingGroupId = $this->setting->get(Setting::DEFAULT_PRICING_GROUP, $domainId);

		return $this->pricingGroupRepository->getById($defaultPricingGroupId);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\PricingGroup
	 */
	public function getDefaultPricingGroupByCurrentDomain() {
		return $this->getDefaultPricingGroupByDomainId($this->domain->getId());
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\PricingGroup
	 */
	public function getDefaultPricingGroupBySelectedDomain() {
		return $this->getDefaultPricingGroupByDomainId($this->selectedDomain->getId());
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 */
	public function setDefaultPricingGroup(PricingGroup $pricingGroup) {
		$this->setting->set(Setting::DEFAULT_PRICING_GROUP, $pricingGroup->getId(), $this->selectedDomain->getId());
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return bool
	 */
	public function isPricingGroupDefault(PricingGroup $pricingGroup) {
		return $pricingGroup === $this->getDefaultPricingGroupBySelectedDomain();
	}

}

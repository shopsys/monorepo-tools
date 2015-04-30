<?php

namespace SS6\ShopBundle\Model\Pricing\Group;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\CustomerEditFacade;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\ProductVisibilityRepository;

class PricingGroupFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository
	 */
	private $pricingGroupRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerEditFacade
	 */
	private $customerEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
	 */
	private $productPriceRecalculationScheduler;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityRepository
	 */
	private $productVisibilityRepository;

	public function __construct(
		EntityManager $em,
		PricingGroupRepository $pricingGroupRepository,
		Domain $domain,
		SelectedDomain $selectedDomain,
		CustomerEditFacade $customerEditFacade,
		ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		ProductVisibilityRepository $productVisibilityRepository
	) {
		$this->em = $em;
		$this->pricingGroupRepository = $pricingGroupRepository;
		$this->domain = $domain;
		$this->selectedDomain = $selectedDomain;
		$this->customerEditFacade = $customerEditFacade;
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->productVisibilityRepository = $productVisibilityRepository;
	}

	/**
	 * @param int $pricingGroupId
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	public function getById($pricingGroupId) {
		return $this->pricingGroupRepository->getById($pricingGroupId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	public function create(PricingGroupData $pricingGroupData) {
		$pricingGroup = new PricingGroup($pricingGroupData, $this->selectedDomain->getId());

		$this->em->persist($pricingGroup);
		$this->em->flush();

		$this->productPriceRecalculationScheduler->scheduleRecalculatePriceForAllProducts();
		$this->productVisibilityRepository->refreshProductVisibilitiesForNewPricingGroup($pricingGroup);

		return $pricingGroup;
	}

	/**
	 * @param type $pricingGroupId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	public function edit($pricingGroupId, PricingGroupData $pricingGroupData) {
		$pricingGroup = $this->pricingGroupRepository->getById($pricingGroupId);
		$pricingGroup->edit($pricingGroupData);

		$this->em->flush();

		$this->productPriceRecalculationScheduler->scheduleRecalculatePriceForAllProducts();

		return $pricingGroup;
	}

	/**
	 * @param int $oldPricingGroupId
	 * @param int|null $newPricingGroupId
	 */
	public function delete($oldPricingGroupId, $newPricingGroupId = null) {
		$oldPricingGroup = $this->pricingGroupRepository->getById($oldPricingGroupId);
		if ($newPricingGroupId !== null) {
			$newPricingGroup = $this->pricingGroupRepository->getById($newPricingGroupId);
		} else {
			$newPricingGroup = null;
		}

		$this->em->beginTransaction();

		if ($newPricingGroup !== null && $this->pricingGroupSettingFacade->isPricingGroupDefault($oldPricingGroup)) {
			$this->pricingGroupSettingFacade->setDefaultPricingGroup($newPricingGroup);
		}
		$this->customerEditFacade->replaceOldPricingGroupWithNewPricingGroup($oldPricingGroup, $newPricingGroup);

		$this->em->remove($oldPricingGroup);
		$this->em->flush();
		$this->em->commit();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	public function getAll() {
		return $this->pricingGroupRepository->getAll();
	}

	/**
	 * @param int $id
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	public function getAllExceptIdByDomainId($id, $domainId) {
		return $this->pricingGroupRepository->getAllExceptIdByDomainId($id, $domainId);
	}

	public function getAllIndexedByDomainId() {
		foreach ($this->domain->getAll() as $domain) {
			$domainId = $domain->getId();
			$pricingGroupsIndexedByDomainId[$domainId] = $this->pricingGroupRepository->getPricingGroupsByDomainId($domainId);
		}

		return $pricingGroupsIndexedByDomainId;
	}

}

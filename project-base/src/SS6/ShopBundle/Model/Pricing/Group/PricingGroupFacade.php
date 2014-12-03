<?php

namespace SS6\ShopBundle\Model\Pricing\Group;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\CustomerEditFacade;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository;
use SS6\ShopBundle\Model\Setting\Setting;

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
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerEditFacade
	 */
	private $customerEditFacade;

	/**
	 *
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository $pricingGroupRepository
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain $selectedDomain
	 * @param \SS6\ShopBundle\Model\Customer\CustomerEditFacade $customerEditFacade
	 * @param \SS6\ShopBundle\Model\Setting\Setting $setting
	 */
	public function __construct(
		EntityManager $em,
		PricingGroupRepository $pricingGroupRepository,
		SelectedDomain $selectedDomain,
		CustomerEditFacade $customerEditFacade,
		Setting $setting
	) {
		$this->em = $em;
		$this->pricingGroupRepository = $pricingGroupRepository;
		$this->selectedDomain = $selectedDomain;
		$this->customerEditFacade = $customerEditFacade;
		$this->setting = $setting;
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

		if ($newPricingGroup !== null && $this->isPricingGroupDefault($oldPricingGroup)) {
			$this->setDefaultPricingGroup($newPricingGroup);
		}
		$this->customerEditFacade->replaceOldPricingGroupWithNewPricingGroup($oldPricingGroup, $newPricingGroup);

		$this->em->remove($oldPricingGroup);
		$this->em->flush();
		$this->em->commit();
	}

	/**
	 * @return SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	public function getAll() {
		return $this->pricingGroupRepository->getAll();
	}

	/**
	 * @return SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	public function getPricingGroupsBySelectedDomainId() {
		return $this->pricingGroupRepository->getPricingGroupsByDomainId($this->selectedDomain->getId());
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return bool
	 */
	public function isPricingGroupUsed(PricingGroup $pricingGroup) {
		return $this->existsUserWithPricingGroup($pricingGroup);
	}

	/**
	 * @param int $id
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	public function getAllExceptIdByDomainId($id, $domainId) {
		return $this->pricingGroupRepository->getAllExceptIdByDomainId($id, $domainId);
	}

	/**
	 * @param PricingGroup $pricingGroup
	 * @return bool
	 */
	private function existsUserWithPricingGroup(PricingGroup $pricingGroup) {
		$query = $this->em->createQuery('
			SELECT COUNT(u)
			FROM ' . User::class . ' u
			WHERE u.pricingGroup = :pricingGroup')
			->setParameter('pricingGroup', $pricingGroup);
		return $query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR) > 0;
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

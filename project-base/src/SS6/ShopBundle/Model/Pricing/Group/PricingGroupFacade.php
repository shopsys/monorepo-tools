<?php

namespace SS6\ShopBundle\Model\Pricing\Group;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\CustomerEditFacade;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository;
use SS6\ShopBundle\Model\Domain\SelectedDomain;

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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository $pricingGroupRepository
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain $selectedDomain
	 * @param \SS6\ShopBundle\Model\Customer\CustomerEditFacade $customerEditFacade
	 */
	public function __construct(
		EntityManager $em,
		PricingGroupRepository $pricingGroupRepository,
		SelectedDomain $selectedDomain,
		CustomerEditFacade $customerEditFacade
	) {
		$this->em = $em;
		$this->pricingGroupRepository = $pricingGroupRepository;
		$this->selectedDomain = $selectedDomain;
		$this->customerEditFacade = $customerEditFacade;
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
	 * @param int $newPricingGroupId
	 */
	public function delete($oldPricingGroupId, $newPricingGroupId) {
		$oldPricingGroup = $this->pricingGroupRepository->getById($oldPricingGroupId);
		$newPricingGroup = $newPricingGroupId ? $this->pricingGroupRepository->findById($newPricingGroupId) : null;

		$this->em->beginTransaction();

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
	 * @param int $domainId
	 * @return SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	public function getPricingGroupsByDomainId($domainId) {
		return $this->pricingGroupRepository->getPricingGroupsByDomainId($domainId);
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
		return 0 < $query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

	}

}

<?php

namespace SS6\ShopBundle\Model\Pricing\Group;

use Doctrine\ORM\EntityManager;
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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository $pricingGroupRepository
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain $selectedDomain
	 */
	public function __construct(
		EntityManager $em,
		PricingGroupRepository $pricingGroupRepository,
		SelectedDomain $selectedDomain
	) {
		$this->em = $em;
		$this->pricingGroupRepository = $pricingGroupRepository;
		$this->selectedDomain = $selectedDomain;
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
	 * @param int $pricingGroupId
	 */
	public function delete($pricingGroupId) {
		$pricingGroup = $this->pricingGroupRepository->getById($pricingGroupId);

		$this->em->remove($pricingGroup);
		$this->em->flush();
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

}

<?php

namespace SS6\ShopBundle\Model\Pricing\Group;

use Doctrine\ORM\EntityManager;

class PricingGroupRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	public function getPricingGroupRepository() {
		return $this->em->getRepository(PricingGroup::class);
	}

	/**
	 * @param int $pricingGroupId
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 * @throws \SS6\ShopBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundExceptoin
	 */
	public function getById($pricingGroupId) {
		$criteria = ['id' => $pricingGroupId];
		$pricingGroup = $this->getPricingGroupRepository()->findOneBy($criteria);
		if ($pricingGroup === null) {
			throw new \SS6\ShopBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundExceptoin($criteria);
		}
		return $pricingGroup;
	}

	/**
	 * @return SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	public function getAll() {
		return $this->getPricingGroupRepository()->findAll();
	}

	/**
	 * @param int $domainId
	 * @return SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	public function getPricingGroupsByDomainId($domainId) {
		return $this->getPricingGroupRepository()->findBy(['domainId' => $domainId]);
	}


}

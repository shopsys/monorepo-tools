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
	private function getPricingGroupRepository() {
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
			throw new \SS6\ShopBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundException($criteria);
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

	/**
	 * @param int $pricingGroupId
	 * @return SS6\ShopBundle\Model\Pricing\Group\PricingGroup|null
	 */
	public function findById($pricingGroupId) {
		return $this->getPricingGroupRepository()->find($pricingGroupId);
	}

	public function getAllExceptIdByDomainId($id, $domainId) {
		$qb = $this->getPricingGroupRepository()->createQueryBuilder('pg')
			->where('pg.domainId = :domainId')
			->andWhere('pg.id != :id')
			->setParameters(['domainId' => $domainId, 'id' => $id]);

		return $qb->getQuery()->getResult();
	}

}

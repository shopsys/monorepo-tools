<?php

namespace SS6\ShopBundle\Model\Administrator\Activity;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivity;
use SS6\ShopBundle\Model\Administrator\Administrator;

class AdministratorActivityRepository {

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
	private function getAdministratorActivityRepository() {
		return $this->em->getRepository(AdministratorActivity::class);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param int $maxResults
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getLastActivitiesQueryBuilder(Administrator $administrator, $maxResults) {
		$lastActivitiesQueryBuilder = $this->getAdministratorActivityRepository()->createQueryBuilder('aa');

		$lastActivitiesQueryBuilder
			->where('aa.administrator = :administrator')->setParameter('administrator', $administrator)
			->orderBy('aa.loginTime', 'DESC')
			->setMaxResults($maxResults);

		return $lastActivitiesQueryBuilder;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @return \SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivity
	 */
	public function getCurrent(Administrator $administrator) {
		$currentAdministratorActvity = $this->getLastActivitiesQueryBuilder($administrator, 1)->getQuery()->getSingleResult();
		if ($currentAdministratorActvity === null) {
			throw new \SS6\ShopBundle\Model\Administrator\Security\Exception\CurrentAdministratorActivityNotFoundException();
		}

		return $currentAdministratorActvity;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param int $maxResults
	 * @return \SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivity[]
	 */
	public function getLastAdministratorActivities(Administrator $administrator, $maxResults) {
		$lastActivitiesQueryBuilder = $this->getLastActivitiesQueryBuilder($administrator, $maxResults);

		return $lastActivitiesQueryBuilder->getQuery()->getResult();
	}

}

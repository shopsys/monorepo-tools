<?php

namespace Shopsys\ShopBundle\Model\Administrator\Activity;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivity;
use Shopsys\ShopBundle\Model\Administrator\Administrator;

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
	 * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
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
	 * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
	 * @return \Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivity
	 */
	public function getCurrent(Administrator $administrator) {
		$currentAdministratorActvity = $this->getLastActivitiesQueryBuilder($administrator, 1)->getQuery()->getSingleResult();
		if ($currentAdministratorActvity === null) {
			throw new \Shopsys\ShopBundle\Model\Administrator\Security\Exception\CurrentAdministratorActivityNotFoundException();
		}

		return $currentAdministratorActvity;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param int $maxResults
	 * @return \Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivity[]
	 */
	public function getLastAdministratorActivities(Administrator $administrator, $maxResults) {
		$lastActivitiesQueryBuilder = $this->getLastActivitiesQueryBuilder($administrator, $maxResults);

		return $lastActivitiesQueryBuilder->getQuery()->getResult();
	}

}

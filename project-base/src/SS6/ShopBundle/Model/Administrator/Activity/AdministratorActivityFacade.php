<?php

namespace SS6\ShopBundle\Model\Administrator\Activity;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivityRepository;
use SS6\ShopBundle\Model\Administrator\Administrator;

class AdministratorActivityFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivityRepository
	 */
	private $administratorActivityRepository;

	public function __construct(
		EntityManager $em,
		AdministratorActivityRepository $administratorActivityRepository
	) {
		$this->em = $em;
		$this->administratorActivityRepository = $administratorActivityRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param string $ipAddress
	 * @return \SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivity
	 */
	public function create(
		Administrator $administrator,
		$ipAddress
	) {
		$administratorActivity = new AdministratorActivity(
			$administrator,
			$ipAddress
		);

		$this->em->persist($administratorActivity);
		$this->em->flush();

		return $administratorActivity;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 */
	public function updateCurrentActivityLastActionTime(Administrator $administrator) {
		$currentAdministratorActivity = $this->administratorActivityRepository->getCurrent($administrator);
		$currentAdministratorActivity->updateLastActionTime();
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param int $maxResults
	 * @return \SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivity[]
	 */
	public function getLastAdministratorActivities(Administrator $administrator, $maxResults) {
		return $this->administratorActivityRepository->getLastAdministratorActivities($administrator, $maxResults);
	}

}

<?php

namespace SS6\ShopBundle\Component\Cron;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Cron\CronModule;

class CronModuleRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getCronModuleRepository() {
		return $this->em->getRepository(CronModule::class);
	}

	/**
	 * @param string $moduleId
	 * @return \SS6\ShopBundle\Component\Cron\CronModule
	 */
	public function getCronModuleByCronModuleId($moduleId) {
		$cronModule = $this->getCronModuleRepository()->find($moduleId);
		if ($cronModule === null) {
			$cronModule = new CronModule($moduleId);
			$this->em->persist($cronModule);
			$this->em->flush($cronModule);
		}

		return $cronModule;
	}

	/**
	 * @return string[]
	 */
	public function getAllScheduledCronModuleIds() {
		$query = $this->em->createQuery('SELECT cm.moduleId FROM ' . CronModule::class . ' cm WHERE cm.scheduled = TRUE');

		return array_map('array_pop', $query->getScalarResult());
	}
}

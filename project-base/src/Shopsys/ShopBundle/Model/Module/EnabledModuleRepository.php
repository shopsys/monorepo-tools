<?php

namespace SS6\ShopBundle\Model\Module;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Module\ModuleList;

class EnabledModuleRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Module\ModuleList
	 */
	private $moduleList;

	public function __construct(
		EntityManager $em,
		ModuleList $moduleList
	) {
		$this->em = $em;
		$this->moduleList = $moduleList;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getEnabledModuleRepository() {
		return $this->em->getRepository(EnabledModule::class);
	}

	/**
	 * @param string $moduleName
	 * @return \SS6\ShopBundle\Model\Module\EnabledModule|null
	 */
	public function findByName($moduleName) {
		if (!in_array($moduleName, $this->moduleList->getValues(), true)) {
			throw new \SS6\ShopBundle\Model\Module\Exception\UnsupportedModuleException($moduleName);
		}

		return $this->getEnabledModuleRepository()->find($moduleName);
	}

}

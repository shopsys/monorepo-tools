<?php

namespace SS6\ShopBundle\Model\Setting;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Setting\SettingValue;

class SettingValueRepository {

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
	private function getSettingValueRepository() {
		return $this->em->getRepository(SettingValue::class);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Setting\SettingValue[]
	 */
	public function findAllForAllDomains() {
		return $this->getSettingValueRepository()->findBy(['domainId' => 0]);
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Setting\SettingValue[]
	 */
	public function findAllByDomainId($domainId) {
		return $this->getSettingValueRepository()->findBy(['domainId' => $domainId]);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Setting\SettingValue[]
	 */
	public function findAllDefault() {
		return $this->getSettingValueRepository()->findBy(['domainId' => null]);
	}
}

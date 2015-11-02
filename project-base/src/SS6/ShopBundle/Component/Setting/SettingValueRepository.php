<?php

namespace SS6\ShopBundle\Component\Setting;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Setting\SettingValue;

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
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Component\Setting\SettingValue[]
	 */
	public function getAllByDomainId($domainId) {
		return $this->getSettingValueRepository()->findBy(['domainId' => $domainId]);
	}

	/**
	 * @return \SS6\ShopBundle\Component\Setting\SettingValue[]
	 */
	public function getAllDefault() {
		return $this->getSettingValueRepository()->findBy(['domainId' => SettingValue::DOMAIN_ID_DEFAULT]);
	}
}

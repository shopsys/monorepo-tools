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
	public function findAll() {
		return $this->getSettingValueRepository()->findAll();
	}
	
}

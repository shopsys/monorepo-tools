<?php

namespace SS6\ShopBundle\Model\Pricing;

use Doctrine\ORM\EntityManager;

class VatRepository {
	
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
	private function getVatRepository() {
		return $this->em->getRepository(Vat::class);
	}
	
	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat[]
	 */
	public function findAll() {
		return $this->getVatRepository()->findAll();
	}

}

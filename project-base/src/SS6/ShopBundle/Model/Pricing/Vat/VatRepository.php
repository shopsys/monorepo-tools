<?php

namespace SS6\ShopBundle\Model\Pricing\Vat;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;

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
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	public function findAll() {
		return $this->getVatRepository()->findAll();
	}

	/**
	 * @param int $vatId
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat|null
	 */
	public function findById($vatId) {
		return $this->getVatRepository()->find($vatId);
	}

	/**
	 * @param int $vatId
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 * @throws \SS6\ShopBundle\Model\Pricing\Vat\Exception\VatNotFoundException
	 */
	public function getById($vatId) {
		$vat = $this->findById($vatId);

		if ($vat === null) {
			throw new \SS6\ShopBundle\Model\Pricing\Vat\Exception\VatNotFoundException($vatId);
		}

		return $vat;
	}

}

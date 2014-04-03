<?php

namespace SS6\CoreBundle\Model\Product\Repository;

use Doctrine\ORM\EntityManager;

class ProductRepository {
	/** @var EntityManager */
	private $entityManager;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}
	
	/**
	 * @param int $id
	 * @return \SS6\CoreBundle\Model\Product\Entity\Product|null
	 */
	public function findById($id) {
		return $this->entityManager->find('SS6\CoreBundle\Model\Product\Entity\Product', $id);
	}
}

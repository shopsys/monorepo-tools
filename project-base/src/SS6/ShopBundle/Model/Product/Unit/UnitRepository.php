<?php

namespace SS6\ShopBundle\Model\Product\Unit;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Unit\Unit;

class UnitRepository {

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->em = $entityManager;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getUnitRepository() {
		return $this->em->getRepository(Unit::class);
	}

	/**
	 * @param int $unitId
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit|null
	 */
	public function findById($unitId) {
		return $this->getUnitRepository()->find($unitId);
	}

	/**
	 * @param int $unitId
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit
	 */
	public function getById($unitId) {
		$unit = $this->findById($unitId);

		if ($unit === null) {
			throw new \SS6\ShopBundle\Model\Product\Unit\Exception\UnitNotFoundException('Unit with ID ' . $unitId . ' not found.');
		}

		return $unit;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit[]
	 */
	public function getAll() {
		return $this->getUnitRepository()->findBy([], ['id' => 'asc']);
	}

}

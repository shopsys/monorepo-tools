<?php

namespace SS6\ShopBundle\Model\Product\Unit;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Model\Product\Unit\Unit;
use SS6\ShopBundle\Model\Product\Unit\UnitData;
use SS6\ShopBundle\Model\Product\Unit\UnitRepository;
use SS6\ShopBundle\Model\Product\Unit\UnitService;

class UnitFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\UnitRepository
	 */
	private $unitRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\UnitService
	 */
	private $unitService;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditFacade
	 */
	private $productEditFacade;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitRepository $unitRepository
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitService $unitService
	 * @param \SS6\ShopBundle\Model\Product\ProductEditFacade $productEditFacade
	 */
	public function __construct(
		EntityManager $em,
		UnitRepository $unitRepository,
		UnitService $unitService,
		ProductEditFacade $productEditFacade
	) {
		$this->em = $em;
		$this->unitRepository = $unitRepository;
		$this->unitService = $unitService;
		$this->productEditFacade = $productEditFacade;
	}

	/**
	 * @param int $unitId
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit
	 */
	public function getById($unitId) {
		return $this->unitRepository->getById($unitId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit
	 */
	public function create(UnitData $unitData) {
		$unit = $this->unitService->create($unitData);
		$this->em->persist($unit);
		$this->em->flush();

		return $unit;
	}

	/**
	 * @param int $unitId
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit
	 */
	public function edit($unitId, UnitData $unitData) {
		$unit = $this->unitRepository->getById($unitId);
		$this->unitService->edit($unit, $unitData);
		$this->em->flush();

		return $unit;
	}

	/**
	 * @param int $unitId
	 * @param int|null $newUnitId
	 */
	public function deleteById($unitId, $newUnitId = null) {
		$oldUnit = $this->unitRepository->getById($unitId);

		if ($newUnitId !== null) {
			$newUnit = $this->unitRepository->getById($newUnitId);
			$this->productEditFacade->replaceOldUnitWithNewUnit($oldUnit, $newUnit);
		}

		$this->em->remove($oldUnit);
		$this->em->flush();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit[]
	 */
	public function getAll() {
		return $this->unitRepository->getAll();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Unit\Unit $unit
	 */
	public function isUnitUsed(Unit $unit) {
		return $this->unitRepository->existsProductWithUnit($unit);
	}

	/**
	 * @param int $unitId
	 * @return string[unitId]
	 */
	public function getUnitNamesByIdExceptId($unitId) {
		$namesById = [];

		$unitsExceptId = $this->unitRepository->getAllExceptId($unitId);
		foreach ($unitsExceptId as $unit) {
			$namesById[$unit->getId()] = $unit->getName();
		}

		return $namesById;
	}

}

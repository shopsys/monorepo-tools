<?php

namespace SS6\ShopBundle\Model\Product\Unit;

use Doctrine\ORM\EntityManager;
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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitRepository $unitRepository
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitService $unitService
	 */
	public function __construct(
		EntityManager $em,
		UnitRepository $unitRepository,
		UnitService $unitService
	) {
		$this->em = $em;
		$this->unitRepository = $unitRepository;
		$this->unitService = $unitService;
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
	 */
	public function deleteById($unitId) {
		$unit = $this->unitRepository->getById($unitId);

		$this->em->remove($unit);
		$this->em->flush();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit[]
	 */
	public function getAll() {
		return $this->unitRepository->getAll();
	}

}

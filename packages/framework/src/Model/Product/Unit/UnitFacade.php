<?php

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Setting\Setting;

class UnitFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\UnitRepository
     */
    private $unitRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\UnitService
     */
    private $unitService;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitRepository $unitRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitService $unitService
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     */
    public function __construct(
        EntityManagerInterface $em,
        UnitRepository $unitRepository,
        UnitService $unitService,
        Setting $setting
    ) {
        $this->em = $em;
        $this->unitRepository = $unitRepository;
        $this->unitService = $unitService;
        $this->setting = $setting;
    }

    /**
     * @param int $unitId
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function getById($unitId)
    {
        return $this->unitRepository->getById($unitId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function create(UnitData $unitData)
    {
        $unit = $this->unitService->create($unitData);
        $this->em->persist($unit);
        $this->em->flush();

        return $unit;
    }

    /**
     * @param int $unitId
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function edit($unitId, UnitData $unitData)
    {
        $unit = $this->unitRepository->getById($unitId);
        $this->unitService->edit($unit, $unitData);
        $this->em->flush();

        return $unit;
    }

    /**
     * @param int $unitId
     * @param int|null $newUnitId
     */
    public function deleteById($unitId, $newUnitId = null)
    {
        $oldUnit = $this->unitRepository->getById($unitId);

        if ($newUnitId !== null) {
            $newUnit = $this->unitRepository->getById($newUnitId);
            $this->unitRepository->replaceUnit($oldUnit, $newUnit);
            if ($this->isUnitDefault($oldUnit)) {
                $this->setDefaultUnit($newUnit);
            }
        }

        $this->em->remove($oldUnit);
        $this->em->flush();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit[]
     */
    public function getAll()
    {
        return $this->unitRepository->getAll();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     * @return bool
     */
    public function isUnitUsed(Unit $unit)
    {
        return $this->unitRepository->existsProductWithUnit($unit);
    }

    /**
     * @param int $unitId
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit[]
     */
    public function getAllExceptId($unitId)
    {
        return $this->unitRepository->getAllExceptId($unitId);
    }

    /**
     * @return int
     */
    private function getDefaultUnitId()
    {
        return $this->setting->get(Setting::DEFAULT_UNIT);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function getDefaultUnit()
    {
        $defaultUnitId = $this->getDefaultUnitId();

        return $this->unitRepository->getById($defaultUnitId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     */
    public function setDefaultUnit(Unit $unit)
    {
        $this->setting->set(Setting::DEFAULT_UNIT, $unit->getId());
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     * @return bool
     */
    public function isUnitDefault(Unit $unit)
    {
        return $this->getDefaultUnit() === $unit;
    }
}

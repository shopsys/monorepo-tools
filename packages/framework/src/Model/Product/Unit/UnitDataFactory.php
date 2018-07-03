<?php

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

class UnitDataFactory implements UnitDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData
     */
    public function create(): UnitData
    {
        return new UnitData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData
     */
    public function createFromUnit(Unit $unit): UnitData
    {
        $unitData = new UnitData();
        $this->fillFromUnit($unitData, $unit);

        return $unitData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     */
    protected function fillFromUnit(UnitData $unitData, Unit $unit)
    {
        $translations = $unit->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $unitData->name = $names;
    }
}

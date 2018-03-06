<?php

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

class UnitService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function create(UnitData $unitData)
    {
        return new Unit($unitData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function edit(Unit $unit, UnitData $unitData)
    {
        $unit->edit($unitData);

        return $unit;
    }
}

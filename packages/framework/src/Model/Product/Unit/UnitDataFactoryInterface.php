<?php

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

interface UnitDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData
     */
    public function create(): UnitData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData
     */
    public function createFromUnit(Unit $unit): UnitData;
}

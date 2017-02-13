<?php

namespace Shopsys\ShopBundle\Model\Product\Unit;

use Shopsys\ShopBundle\Model\Product\Unit\Unit;
use Shopsys\ShopBundle\Model\Product\Unit\UnitData;

class UnitService
{
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
     * @return \Shopsys\ShopBundle\Model\Product\Unit\Unit
     */
    public function create(UnitData $unitData)
    {
        return new Unit($unitData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Unit\Unit $unit
     * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
     * @return \Shopsys\ShopBundle\Model\Product\Unit\Unit
     */
    public function edit(Unit $unit, UnitData $unitData)
    {
        $unit->edit($unitData);

        return $unit;
    }
}

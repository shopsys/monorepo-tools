<?php

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

class UnitFactory implements UnitFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function create(UnitData $data): Unit
    {
        return new Unit($data);
    }
}

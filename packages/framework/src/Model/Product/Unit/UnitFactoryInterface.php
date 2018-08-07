<?php

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

interface UnitFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function create(UnitData $data): Unit;
}

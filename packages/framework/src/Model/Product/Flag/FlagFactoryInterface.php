<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag;

interface FlagFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag
     */
    public function create(FlagData $data): Flag;
}

<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag;

interface FlagDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData
     */
    public function create(): FlagData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag $flag
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData
     */
    public function createFromFlag(Flag $flag): FlagData;
}

<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag;

class FlagService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData $flagData
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag
     */
    public function create(FlagData $flagData)
    {
        return new Flag($flagData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag $flag
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData $flagData
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag
     */
    public function edit(Flag $flag, FlagData $flagData)
    {
        $flag->edit($flagData);

        return $flag;
    }
}

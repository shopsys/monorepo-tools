<?php

namespace Shopsys\ShopBundle\Model\Product\Flag;

use Shopsys\ShopBundle\Model\Product\Flag\Flag;
use Shopsys\ShopBundle\Model\Product\Flag\FlagData;

class FlagService
{
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagData $flagData
     * @return \Shopsys\ShopBundle\Model\Product\Flag\Flag
     */
    public function create(FlagData $flagData)
    {
        return new Flag($flagData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Flag\Flag $flag
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagData $flagData
     * @return \Shopsys\ShopBundle\Model\Product\Flag\Flag
     */
    public function edit(Flag $flag, FlagData $flagData)
    {
        $flag->edit($flagData);

        return $flag;
    }
}

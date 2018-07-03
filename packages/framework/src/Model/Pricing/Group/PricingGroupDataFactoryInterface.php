<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Group;

interface PricingGroupDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData
     */
    public function create(): PricingGroupData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData
     */
    public function createFromPricingGroup(PricingGroup $pricingGroup): PricingGroupData;
}

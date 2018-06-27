<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Group;

class PricingGroupDataFactory implements PricingGroupDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData
     */
    public function create(): PricingGroupData
    {
        return new PricingGroupData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData
     */
    public function createFromPricingGroup(PricingGroup $pricingGroup): PricingGroupData
    {
        $pricingGroupData = new PricingGroupData();
        $this->fillFromPricingGroup($pricingGroupData, $pricingGroup);

        return $pricingGroupData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     */
    protected function fillFromPricingGroup(PricingGroupData $pricingGroupData, PricingGroup $pricingGroup)
    {
        $pricingGroupData->name = $pricingGroup->getName();
        $pricingGroupData->coefficient = $pricingGroup->getCoefficient();
    }
}

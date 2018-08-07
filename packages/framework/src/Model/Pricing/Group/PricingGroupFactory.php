<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Group;

class PricingGroupFactory implements PricingGroupFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData $data
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup
     */
    public function create(PricingGroupData $data, int $domainId): PricingGroup
    {
        return new PricingGroup($data, $domainId);
    }
}

<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Group;

interface PricingGroupFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData $data
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup
     */
    public function create(PricingGroupData $data, int $domainId): PricingGroup;
}

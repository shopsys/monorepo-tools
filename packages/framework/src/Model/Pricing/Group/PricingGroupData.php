<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Group;

class PricingGroupData
{
    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string
     */
    public $coefficient;

    public function __construct()
    {
        $this->coefficient = '1';
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     */
    public function setFromEntity(PricingGroup $pricingGroup)
    {
        $this->name = $pricingGroup->getName();
        $this->coefficient = $pricingGroup->getCoefficient();
    }
}

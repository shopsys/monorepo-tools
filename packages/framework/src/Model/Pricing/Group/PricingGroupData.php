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

    /**
     * @param string|null $name
     * @param string $coefficient
     */
    public function __construct($name = null, $coefficient = '1')
    {
        $this->name = $name;
        $this->coefficient = $coefficient;
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

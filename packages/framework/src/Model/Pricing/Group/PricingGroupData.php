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
}

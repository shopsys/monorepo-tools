<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

class AvailabilityData
{
    /**
     * @var string[]|null[]
     */
    public $name;

    /**
     * @var int|null
     */
    public $dispatchTime;

    public function __construct()
    {
        $this->name = [];
    }
}

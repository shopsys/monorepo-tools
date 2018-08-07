<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

interface AvailabilityFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\Availability
     */
    public function create(AvailabilityData $data): Availability;
}

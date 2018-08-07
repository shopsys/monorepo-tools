<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

class AvailabilityFactory implements AvailabilityFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\Availability
     */
    public function create(AvailabilityData $data): Availability
    {
        return new Availability($data);
    }
}

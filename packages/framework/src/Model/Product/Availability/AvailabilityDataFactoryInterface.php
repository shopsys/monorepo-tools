<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

interface AvailabilityDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData
     */
    public function create(): AvailabilityData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\Availability $availability
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData
     */
    public function createFromAvailability(Availability $availability): AvailabilityData;
}

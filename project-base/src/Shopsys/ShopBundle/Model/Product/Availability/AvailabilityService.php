<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

class AvailabilityService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\Availability
     */
    public function create(AvailabilityData $availabilityData)
    {
        return new Availability($availabilityData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\Availability $availability
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\Availability
     */
    public function edit(Availability $availability, AvailabilityData $availabilityData)
    {
        $availability->edit($availabilityData);

        return $availability;
    }
}

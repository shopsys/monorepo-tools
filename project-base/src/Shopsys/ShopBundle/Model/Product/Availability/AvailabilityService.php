<?php

namespace Shopsys\ShopBundle\Model\Product\Availability;

use Shopsys\ShopBundle\Model\Product\Availability\Availability;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData;

class AvailabilityService
{
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
     * @return \Shopsys\ShopBundle\Model\Product\Availability\Availability
     */
    public function create(AvailabilityData $availabilityData)
    {
        return new Availability($availabilityData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Availability\Availability $availability
     * @param \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
     * @return \Shopsys\ShopBundle\Model\Product\Availability\Availability
     */
    public function edit(Availability $availability, AvailabilityData $availabilityData)
    {
        $availability->edit($availabilityData);

        return $availability;
    }
}

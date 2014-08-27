<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;

class AvailabilityService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability
	 */
	public function create(AvailabilityData $availabilityData) {
		return new Availability($availabilityData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability
	 */
	public function edit(Availability $availability, AvailabilityData $availabilityData) {
		$availability->edit($availabilityData);

		return $availability;
	}

}

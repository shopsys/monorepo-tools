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

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $productsWithOldAvailability
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $oldAvailability
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $newAvailability
	 */
	public function delete(
		array $productsWithOldAvailability,
		Availability $oldAvailability,
		Availability $newAvailability
	) {
		if (count($productsWithOldAvailability) > 0) {
			$this->changeProductsAvailabilities($productsWithOldAvailability, $oldAvailability, $newAvailability);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $oldAvailability
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $newAvailability
	 */
	private function changeProductsAvailabilities(
		array $products,
		Availability $oldAvailability,
		Availability $newAvailability
	) {
		foreach ($products as $product) {
			/* @var $product \SS6\ShopBundle\Model\Product\Product */
			if ($product->getAvailability() === $oldAvailability) {
				$product->setAvailability($newAvailability);
			}
			if ($product->getOutOfStockAvailability() === $oldAvailability) {
				$product->setOutOfStockAvailability($newAvailability);
			}
			$product->setCalculatedAvailability($newAvailability);
		}
	}

}

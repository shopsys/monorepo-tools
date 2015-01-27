<?php

namespace SS6\ShopBundle\Model\Product\Availability;

class AvailabilityData {

	/**
	 * @var string[]
	 */
	public $name;

	/**
	 * @var int|null
	 */
	public $deliveryTime;

	/**
	 * @param string[] $name
	 * @param int|null $deliveryTime
	 */
	public function __construct(array $name = [], $deliveryTime = null) {
		$this->deliveryTime = $deliveryTime;
		$this->name = $name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 */
	public function setFromEntity(Availability $availability) {
		$this->deliveryTime = $availability->getDeliveryTime();
		$translations = $availability->getTranslations();
		$names = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->name = $names;
	}

}

<?php

namespace SS6\ShopBundle\Model\Product\Availability;

class AvailabilityData {

	/**
	 * @var string[]
	 */
	public $name;

	/**
	 * @param string[] $name
	 */
	public function __construct(array $name = []) {
		$this->name = $name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 */
	public function setFromEntity(Availability $availability) {
		$translations = $availability->getTranslations();
		$names = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->name = $names;
	}

}

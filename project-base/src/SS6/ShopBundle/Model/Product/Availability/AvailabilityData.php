<?php

namespace SS6\ShopBundle\Model\Product\Availability;

class AvailabilityData {

	/**
	 * @var array
	 */
	private $names;

	/**
	 * @param array $names
	 */
	public function __construct($names = array()) {
		$this->names = $names;
	}

	/**
	 * @return array
	 */
	public function getNames() {
		return $this->names;
	}

	/**
	 * @param array $names
	 */
	public function setNames($names) {
		$this->names = $names;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 */
	public function setFromEntity(Availability $availability) {
		$translations = $availability->getTranslations();
		$names = array();
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->setNames($names);
	}

}

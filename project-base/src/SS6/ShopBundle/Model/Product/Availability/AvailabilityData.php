<?php

namespace SS6\ShopBundle\Model\Product\Availability;

class AvailabilityData {

	/**
	 * @var string|null
	 */
	private $name;

	/**
	 * @param string|null $name
	 */
	public function __construct($name = null) {
		$this->name = $name;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 */
	public function setFromEntity(Availability $availability) {
		$this->name = $availability->getName();
	}
	
}

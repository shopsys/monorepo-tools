<?php

namespace SS6\ShopBundle\Model\Country;

class CountryData {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var bool
	 */
	public $visible;

	/**
	 * @param string $name
	 * @param bool $visible
	 */
	public function __construct($name = '', $visible = true) {
		$this->name = $name;
		$this->visible = $visible;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Country\Country $country
	 */
	public function setFromEntity(Country $country) {
		$this->name = $country->getName();
		$this->visible = $country->isVisible();
	}

}

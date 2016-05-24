<?php

namespace SS6\ShopBundle\Model\Country;

class CountryData {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @param string $name
	 */
	public function __construct($name = '') {
		$this->name = $name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Country\Country $country
	 */
	public function setFromEntity(Country $country) {
		$this->name = $country->getName();
	}

}

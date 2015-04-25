<?php

namespace SS6\ShopBundle\Model\Advert;

class AdvertPosition {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $localizedName;

	/**
	 * @param string $name
	 * @param string $localizedName
	 */
	public function __construct($name, $localizedName) {
		$this->name = $name;
		$this->localizedName = $localizedName;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getLocalizedName() {
		return $this->localizedName;
	}

}

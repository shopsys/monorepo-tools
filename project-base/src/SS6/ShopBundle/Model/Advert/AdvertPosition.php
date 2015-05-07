<?php

namespace SS6\ShopBundle\Model\Advert;

class AdvertPosition {

	const POSITION_HEADER = 'header';
	const POSITION_FOOTER = 'footer';
	const POSITION_PRODUCT_LIST = 'productList';
	const POSITION_LEFT_SIDEBAR = 'leftSidebar';

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

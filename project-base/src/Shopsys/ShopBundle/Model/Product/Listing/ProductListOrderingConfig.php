<?php

namespace Shopsys\ShopBundle\Model\Product\Listing;

class ProductListOrderingConfig {

	/**
	 * @var string[orderingMode]
	 */
	private $supportedOrderingModesNames;

	/**
	 * @var string
	 */
	private $defaultOrderingMode;

	/**
	 * @var string
	 */
	private $cookieName;

	/**
	 * @param string[orderingMode] $supportedOrderingModesNames
	 * @param string $defaultOrderingMode
	 * @param string $cookieName
	 */
	public function __construct($supportedOrderingModesNames, $defaultOrderingMode, $cookieName) {
		$this->supportedOrderingModesNames = $supportedOrderingModesNames;
		$this->defaultOrderingMode = $defaultOrderingMode;
		$this->cookieName = $cookieName;
	}

	/**
	 * @return string[orderingMode]
	 */
	public function getSupportedOrderingModesNames() {
		return $this->supportedOrderingModesNames;
	}

	/**
	 * @return string
	 */
	public function getCookieName() {
		return $this->cookieName;
	}

	/**
	 * @return string
	 */
	public function getDefaultOrderingMode() {
		return $this->defaultOrderingMode;
	}

}

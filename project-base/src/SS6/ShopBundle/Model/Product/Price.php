<?php

namespace SS6\ShopBundle\Model\Product;

class Price {

	/**
	 * @var string
	 */
	private $basePriceWithoutVat;

	/**
	 * @var string
	 */
	private $basePriceWithVat;

	/**
	 * @param string $basePriceWithoutVat
	 * @param string $basePriceWithVat
	 */
	public function __construct($basePriceWithoutVat, $basePriceWithVat) {
		$this->basePriceWithoutVat = $basePriceWithoutVat;
		$this->basePriceWithVat = $basePriceWithVat;
	}

	/**
	 * @return string
	 */
	public function getBasePriceWithoutVat() {
		return $this->basePriceWithoutVat;
	}

	/**
	 * @return string
	 */
	public function getBasePriceWithVat() {
		return $this->basePriceWithVat;
	}

}

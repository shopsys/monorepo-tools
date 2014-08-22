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
	 * @var string
	 */
	private $basePriceVatAmount;

	/**
	 * @param string $basePriceWithoutVat
	 * @param string $basePriceWithVat
	 * @param string $basePriceVatAmount
	 */
	public function __construct($basePriceWithoutVat, $basePriceWithVat, $basePriceVatAmount) {
		$this->basePriceWithoutVat = $basePriceWithoutVat;
		$this->basePriceWithVat = $basePriceWithVat;
		$this->basePriceVatAmount = $basePriceVatAmount;
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

	/**
	 * @return string
	 */
	public function getBasePriceVatAmount() {
		return $this->basePriceVatAmount;
	}

}

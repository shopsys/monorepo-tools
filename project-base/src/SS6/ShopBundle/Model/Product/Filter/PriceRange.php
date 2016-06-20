<?php

namespace SS6\ShopBundle\Model\Product\Filter;

class PriceRange {

	/**
	 * @var string
	 */
	private $minimalPrice;

	/**
	 * @var string
	 */
	private $maximalPrice;

	/**
	 * @param string $minimalPrice
	 * @param string $maximalPrice
	 */
	public function __construct($minimalPrice, $maximalPrice) {
		$this->minimalPrice = $minimalPrice;
		$this->maximalPrice = $maximalPrice;
	}

	/**
	 * @return string
	 */
	public function getMinimalPrice() {
		return $this->minimalPrice;
	}

	/**
	 * @return string
	 */
	public function getMaximalPrice() {
		return $this->maximalPrice;
	}

}

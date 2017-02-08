<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Pricing\Price;

class ProductPrice extends Price {

	/**
	 * @var bool
	 */
	private $priceFrom;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Price $price
	 * @param bool $priceFrom
	 */
	public function __construct(Price $price, $priceFrom) {
		$this->priceFrom = $priceFrom;
		parent::__construct($price->getPriceWithoutVat(), $price->getPriceWithVat());
	}

	/**
	 * @return bool
	 */
	public function isPriceFrom() {
		return $this->priceFrom;
	}

}

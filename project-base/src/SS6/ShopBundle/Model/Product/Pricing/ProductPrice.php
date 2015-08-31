<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Pricing\Price;

class ProductPrice extends Price {

	/**
	 * @var bool
	 */
	private $from;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Price $price
	 * @param bool $from
	 */
	public function __construct(Price $price, $from) {
		$this->from = $from;
		parent::__construct($price->getPriceWithoutVat(), $price->getPriceWithVat(), $price->getVatAmount());
	}

	/**
	 * @return bool
	 */
	public function isFrom() {
		return $this->from;
	}

}

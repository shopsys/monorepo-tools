<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Price;

class ProductSellingPrice {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	private $pricingGroup;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $sellingPrice;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param \SS6\ShopBundle\Model\Pricing\Price $sellingPrice
	 */
	public function __construct(PricingGroup $pricingGroup, Price $sellingPrice) {
		$this->pricingGroup = $pricingGroup;
		$this->sellingPrice = $sellingPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	public function getPricingGroup() {
		return $this->pricingGroup;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getSellingPrice() {
		return $this->sellingPrice;
	}

}

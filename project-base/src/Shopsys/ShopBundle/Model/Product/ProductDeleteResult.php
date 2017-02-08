<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Product\Product;

class ProductDeleteResult {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product[]
	 */
	private $productsForRecalculations;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $productsForRecalculations
	 */
	public function __construct(array $productsForRecalculations = []) {
		$this->productsForRecalculations = $productsForRecalculations;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getProductsForRecalculations() {
		return $this->productsForRecalculations;
	}
}

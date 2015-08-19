<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Product\Product;

class ProductDeleteResult {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product|null
	 */
	private $productForRecalculations;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product|null $productForRecalculations
	 */
	public function __construct(Product $productForRecalculations = null) {
		$this->productForRecalculations = $productForRecalculations;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product|null
	 */
	public function getProductForRecalculations() {
		return $this->productForRecalculations;
	}
}

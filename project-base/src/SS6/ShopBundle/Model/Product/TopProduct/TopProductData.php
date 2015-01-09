<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use SS6\ShopBundle\Model\Product\Product;

class TopProductData {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 */
	public $product;

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Product\Product|null $product
	 */
	public function __construct($product = null) {
		$this->product = $product;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProduct $topProduct
	 */
	public function setFromEntity(TopProduct $topProduct) {
		$this->id = $topProduct->getId();
		$this->product = $topProduct->getProduct();
	}

}

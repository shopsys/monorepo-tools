<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use SS6\ShopBundle\Model\Product\Product;

class TopProductData {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 */
	private $product;

	/**
	 * @param int $domainId
	 * @param int $id
	 * @param \SS6\ShopBundle\Model\Product\Product|null $product
	 */
	public function __construct($id = null, $product = null) {
		$this->id = $id;
		$this->product = $product;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function setProduct(Product $product) {
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

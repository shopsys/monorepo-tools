<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use SS6\ShopBundle\Model\Product\Product;

class ProductParameterValueData {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	private $parameter;

	/**
	 * @var string
	 */
	private $value;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue $productParameterValue
	 */
	public function setFromEntity(ProductParameterValue $productParameterValue) {
		$this->product = $productParameterValue->getProduct();
		$this->parameter = $productParameterValue->getParameter();
		$this->value = $productParameterValue->getValue();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public function getParameter() {
		return $this->parameter;
	}

	/**
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function setProduct(Product $product) {
		$this->product = $product;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\Parameter $parameter
	 */
	public function setParameter(Parameter $parameter) {
		$this->parameter = $parameter;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}

}

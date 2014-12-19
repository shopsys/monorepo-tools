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
	private $locale;

	/**
	 * @var string|null
	 */
	private $valueText;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue $productParameterValue
	 */
	public function setFromEntity(ProductParameterValue $productParameterValue) {
		$this->product = $productParameterValue->getProduct();
		$this->parameter = $productParameterValue->getParameter();
		$this->locale = $productParameterValue->getLocale();
		$this->valueText = $productParameterValue->getValue()->getText();
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
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * @return string|null
	 */
	public function getValueText() {
		return $this->valueText;
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
	 * @param string $locale
	 */
	public function setLocale($locale) {
		$this->locale = $locale;
	}

	/**
	 * @param string|null $valueText
	 */
	public function setValueText($valueText) {
		$this->valueText = $valueText;
	}

}

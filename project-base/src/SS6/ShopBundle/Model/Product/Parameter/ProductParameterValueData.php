<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

class ProductParameterValueData {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 */
	public $product;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public $parameter;

	/**
	 * @var string
	 */
	public $locale;

	/**
	 * @var string|null
	 */
	public $valueText;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue $productParameterValue
	 */
	public function setFromEntity(ProductParameterValue $productParameterValue) {
		$this->product = $productParameterValue->getProduct();
		$this->parameter = $productParameterValue->getParameter();
		$this->locale = $productParameterValue->getValue()->getLocale();
		$this->valueText = $productParameterValue->getValue()->getText();
	}

}

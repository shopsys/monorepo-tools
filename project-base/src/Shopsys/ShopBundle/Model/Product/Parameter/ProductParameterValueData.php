<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

class ProductParameterValueData {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter|null
	 */
	public $parameter;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterValueData|null
	 */
	public $parameterValueData;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue $productParameterValue
	 */
	public function setFromEntity(ProductParameterValue $productParameterValue) {
		$this->parameter = $productParameterValue->getParameter();
		$this->parameterValueData = new ParameterValueData();
		$this->parameterValueData->setFromEntity($productParameterValue->getValue());
	}

}

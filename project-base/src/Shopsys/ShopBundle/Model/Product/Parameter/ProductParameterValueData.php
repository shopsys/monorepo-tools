<?php

namespace Shopsys\ShopBundle\Model\Product\Parameter;

class ProductParameterValueData
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\Parameter|null
     */
    public $parameter;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterValueData|null
     */
    public $parameterValueData;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValue $productParameterValue
     */
    public function setFromEntity(ProductParameterValue $productParameterValue) {
        $this->parameter = $productParameterValue->getParameter();
        $this->parameterValueData = new ParameterValueData();
        $this->parameterValueData->setFromEntity($productParameterValue->getValue());
    }
}

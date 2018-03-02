<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

class ProductParameterValueData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter|null
     */
    public $parameter;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueData|null
     */
    public $parameterValueData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue $productParameterValue
     */
    public function setFromEntity(ProductParameterValue $productParameterValue)
    {
        $this->parameter = $productParameterValue->getParameter();
        $this->parameterValueData = new ParameterValueData();
        $this->parameterValueData->setFromEntity($productParameterValue->getValue());
    }
}

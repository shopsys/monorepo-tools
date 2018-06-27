<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

interface ProductParameterValueDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData
     */
    public function create(): ProductParameterValueData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue $productParameterValue
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData
     */
    public function createFromProductParameterValue(ProductParameterValue $productParameterValue): ProductParameterValueData;
}

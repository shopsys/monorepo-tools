<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

use Shopsys\FrameworkBundle\Model\Product\Product;

interface ProductParameterValueFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue $value
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue
     */
    public function create(
        Product $product,
        Parameter $parameter,
        ParameterValue $value
    ): ProductParameterValue;
}

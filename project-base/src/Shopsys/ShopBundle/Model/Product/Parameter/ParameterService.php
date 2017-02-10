<?php

namespace Shopsys\ShopBundle\Model\Product\Parameter;

use Shopsys\ShopBundle\Model\Product\Parameter\Parameter;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterData;

class ParameterService
{
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     */
    public function create(ParameterData $parameterData) {
        return new Parameter($parameterData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\Parameter $parameter
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     */
    public function edit(Parameter $parameter, ParameterData $parameterData) {
        $parameter->edit($parameterData);

        return $parameter;
    }
}

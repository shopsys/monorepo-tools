<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

class ParameterService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData $parameterData
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter
     */
    public function create(ParameterData $parameterData)
    {
        return new Parameter($parameterData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData $parameterData
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter
     */
    public function edit(Parameter $parameter, ParameterData $parameterData)
    {
        $parameter->edit($parameterData);

        return $parameter;
    }
}

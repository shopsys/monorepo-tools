<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

interface ParameterValueDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueData
     */
    public function create(): ParameterValueData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue $parameterValue
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueData
     */
    public function createFromParameterValue(ParameterValue $parameterValue): ParameterValueData;
}

<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

interface ParameterDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData
     */
    public function create(): ParameterData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData
     */
    public function createFromParameter(Parameter $parameter): ParameterData;
}

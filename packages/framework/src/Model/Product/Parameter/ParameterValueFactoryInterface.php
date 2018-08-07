<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

interface ParameterValueFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueData $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue
     */
    public function create(ParameterValueData $data): ParameterValue;
}

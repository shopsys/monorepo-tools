<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

interface ParameterFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter
     */
    public function create(ParameterData $data): Parameter;
}

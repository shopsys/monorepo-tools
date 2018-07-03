<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

class ParameterFilterData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter|null
     */
    public $parameter;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue[]
     */
    public $values = [];
}

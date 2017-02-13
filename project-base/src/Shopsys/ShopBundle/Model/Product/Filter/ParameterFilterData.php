<?php

namespace Shopsys\ShopBundle\Model\Product\Filter;

use Shopsys\ShopBundle\Model\Product\Parameter\Parameter;

class ParameterFilterData
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     */
    public $parameter;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue[]
     */
    public $values = [];

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\Parameter $parameter
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue[] $values
     */
    public function __construct(
        Parameter $parameter = null,
        array $values = []
    ) {
        $this->parameter = $parameter;
        $this->values = $values;
    }
}

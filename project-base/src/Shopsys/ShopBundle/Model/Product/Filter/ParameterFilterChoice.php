<?php

namespace Shopsys\ShopBundle\Model\Product\Filter;

use Shopsys\ShopBundle\Model\Product\Parameter\Parameter;

class ParameterFilterChoice
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     */
    private $parameter;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue[]
     */
    private $values = [];

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

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     */
    public function getParameter() {
        return $this->parameter;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue[]
     */
    public function getValues() {
        return $this->values;
    }

}

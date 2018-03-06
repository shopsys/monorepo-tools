<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

use Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter;

class ParameterFilterChoice
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter
     */
    private $parameter;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue[]
     */
    private $values;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue[] $values
     */
    public function __construct(
        Parameter $parameter = null,
        array $values = []
    ) {
        $this->parameter = $parameter;
        $this->values = $values;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue[]
     */
    public function getValues()
    {
        return $this->values;
    }
}

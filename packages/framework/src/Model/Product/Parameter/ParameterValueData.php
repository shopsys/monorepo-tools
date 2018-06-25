<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

class ParameterValueData
{
    /**
     * @var string|null
     */
    public $text;

    /**
     * @var string|null
     */
    public $locale;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue $parameterValue
     */
    public function setFromEntity(ParameterValue $parameterValue)
    {
        $this->text = $parameterValue->getText();
        $this->locale = $parameterValue->getLocale();
    }
}

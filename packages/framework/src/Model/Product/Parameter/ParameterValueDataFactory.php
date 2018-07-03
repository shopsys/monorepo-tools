<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

class ParameterValueDataFactory implements ParameterValueDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueData
     */
    public function create(): ParameterValueData
    {
        return new ParameterValueData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue $parameterValue
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueData
     */
    public function createFromParameterValue(ParameterValue $parameterValue): ParameterValueData
    {
        $parameterValueData = new ParameterValueData();
        $this->fillFromParameterValue($parameterValueData, $parameterValue);

        return $parameterValueData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueData $parameterValueData
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue $parameterValue
     */
    protected function fillFromParameterValue(ParameterValueData $parameterValueData, ParameterValue $parameterValue)
    {
        $parameterValueData->text = $parameterValue->getText();
        $parameterValueData->locale = $parameterValue->getLocale();
    }
}

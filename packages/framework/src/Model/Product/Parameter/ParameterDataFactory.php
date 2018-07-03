<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

class ParameterDataFactory implements ParameterDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData
     */
    public function create(): ParameterData
    {
        return new ParameterData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData
     */
    public function createFromParameter(Parameter $parameter): ParameterData
    {
        $parameterData = new ParameterData();
        $this->fillFromParameter($parameterData, $parameter);

        return $parameterData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData $parameterData
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     */
    protected function fillFromParameter(ParameterData $parameterData, Parameter $parameter)
    {
        $translations = $parameter->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $parameterData->name = $names;
        $parameterData->visible = $parameter->isVisible();
    }
}

<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

use Shopsys\FrameworkBundle\Component\Domain\Domain;

class ParameterDataFactory implements ParameterDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData
     */
    public function create(): ParameterData
    {
        $parameterData = new ParameterData();
        $this->fillNew($parameterData);
        return $parameterData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData $parameterData
     */
    protected function fillNew(ParameterData $parameterData): void
    {
        foreach ($this->domain->getAllLocales() as $locale) {
            $parameterData->name[$locale] = null;
        }
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
        /** @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterTranslation[] $translations */
        $translations = $parameter->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $parameterData->name = $names;
        $parameterData->visible = $parameter->isVisible();
    }
}

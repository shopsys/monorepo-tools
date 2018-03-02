<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

class ParameterData
{
    /**
     * @var string[]
     */
    public $name;

    /**
     * @var bool
     */
    public $visible;

    /**
     * @param string[] $name
     * @param bool $visible
     */
    public function __construct(array $name = [], $visible = false)
    {
        $this->name = $name;
        $this->visible = $visible;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     */
    public function setFromEntity(Parameter $parameter)
    {
        $translations = $parameter->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $this->name = $names;
        $this->visible = $parameter->isVisible();
    }
}

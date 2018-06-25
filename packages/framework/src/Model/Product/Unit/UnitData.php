<?php

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

class UnitData
{
    /**
     * @var string[]
     */
    public $name;

    public function __construct()
    {
        $this->name = [];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     */
    public function setFromEntity(Unit $unit)
    {
        $translations = $unit->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $this->name = $names;
    }
}

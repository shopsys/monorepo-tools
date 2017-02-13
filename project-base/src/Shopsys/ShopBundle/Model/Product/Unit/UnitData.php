<?php

namespace Shopsys\ShopBundle\Model\Product\Unit;

class UnitData
{
    /**
     * @var string[]
     */
    public $name;

    /**
     * @param array $name
     */
    public function __construct(array $name = [])
    {
        $this->name = $name;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Unit\Unit $unit
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

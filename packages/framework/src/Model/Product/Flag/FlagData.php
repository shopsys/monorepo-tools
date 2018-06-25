<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag;

class FlagData
{
    /**
     * @var string[]
     */
    public $name;

    /**
     * @var string|null
     */
    public $rgbColor;

    /**
     * @var bool
     */
    public $visible;

    public function __construct()
    {
        $this->name = [];
        $this->visible = false;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag $flag
     */
    public function setFromEntity(Flag $flag)
    {
        $translations = $flag->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $this->name = $names;
        $this->rgbColor = $flag->getRgbColor();
        $this->visible = $flag->isVisible();
    }
}

<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

class AvailabilityData
{
    /**
     * @var string[]
     */
    public $name;

    /**
     * @var int|null
     */
    public $dispatchTime;

    public function __construct()
    {
        $this->name = [];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\Availability $availability
     */
    public function setFromEntity(Availability $availability)
    {
        $this->dispatchTime = $availability->getDispatchTime();
        $translations = $availability->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $this->name = $names;
    }
}

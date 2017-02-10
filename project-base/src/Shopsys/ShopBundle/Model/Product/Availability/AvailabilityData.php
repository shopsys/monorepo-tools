<?php

namespace Shopsys\ShopBundle\Model\Product\Availability;

class AvailabilityData {

    /**
     * @var string[]
     */
    public $name;

    /**
     * @var int|null
     */
    public $dispatchTime;

    /**
     * @param string[] $name
     * @param int|null $dispatchTime
     */
    public function __construct(array $name = [], $dispatchTime = null) {
        $this->dispatchTime = $dispatchTime;
        $this->name = $name;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Availability\Availability $availability
     */
    public function setFromEntity(Availability $availability) {
        $this->dispatchTime = $availability->getDispatchTime();
        $translations = $availability->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $this->name = $names;
    }

}

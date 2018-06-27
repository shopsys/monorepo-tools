<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

class AvailabilityDataFactory implements AvailabilityDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData
     */
    public function create(): AvailabilityData
    {
        return new AvailabilityData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\Availability $availability
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData
     */
    public function createFromAvailability(Availability $availability): AvailabilityData
    {
        $availabilityData = new AvailabilityData();
        $this->fillFromAvailability($availabilityData, $availability);

        return $availabilityData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\Availability $availability
     */
    protected function fillFromAvailability(AvailabilityData $availabilityData, Availability $availability)
    {
        $availabilityData->dispatchTime = $availability->getDispatchTime();
        $translations = $availability->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $availabilityData->name = $names;
    }
}

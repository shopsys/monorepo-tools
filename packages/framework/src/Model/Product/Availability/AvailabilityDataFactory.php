<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

use Shopsys\FrameworkBundle\Component\Domain\Domain;

class AvailabilityDataFactory implements AvailabilityDataFactoryInterface
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
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData
     */
    public function create(): AvailabilityData
    {
        $availabilityData = new AvailabilityData();
        $this->fillNew($availabilityData);
        return $availabilityData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
     */
    protected function fillNew(AvailabilityData $availabilityData): void
    {
        foreach ($this->domain->getAllLocales() as $locale) {
            $availabilityData->name[$locale] = null;
        }
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

        /** @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityTranslation[] $translations */
        $translations = $availability->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $availabilityData->name = $names;
    }
}

<?php

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

use Shopsys\FrameworkBundle\Component\Domain\Domain;

class UnitDataFactory implements UnitDataFactoryInterface
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
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData
     */
    public function create(): UnitData
    {
        $unitData = new UnitData();
        $this->fillNew($unitData);
        return $unitData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     */
    protected function fillNew(UnitData $unitData): void
    {
        foreach ($this->domain->getAllLocales() as $locale) {
            $unitData->name[$locale] = null;
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData
     */
    public function createFromUnit(Unit $unit): UnitData
    {
        $unitData = new UnitData();
        $this->fillFromUnit($unitData, $unit);

        return $unitData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     */
    protected function fillFromUnit(UnitData $unitData, Unit $unit)
    {
        /** @var \Shopsys\FrameworkBundle\Model\Product\Unit\UnitTranslation[] $translations */
        $translations = $unit->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $unitData->name = $names;
    }
}

<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitData;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade;

class UnitDataFixture extends AbstractReferenceFixture
{
    const UNIT_CUBIC_METERS = 'unit_m3';
    const UNIT_PIECES = 'unit_pcs';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade
     */
    private $unitFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\UnitDataFactoryInterface
     */
    private $unitDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade $unitFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitDataFactoryInterface $unitDataFactory
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     */
    public function __construct(
        UnitFacade $unitFacade,
        UnitDataFactoryInterface $unitDataFactory,
        Setting $setting
    ) {
        $this->unitFacade = $unitFacade;
        $this->unitDataFactory = $unitDataFactory;
        $this->setting = $setting;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $unitData = $this->unitDataFactory->create();

        $unitData->name = ['cs' => 'm³', 'en' => 'm³'];
        $this->createUnit($unitData, self::UNIT_CUBIC_METERS);

        $unitData->name = ['cs' => 'ks', 'en' => 'pcs'];
        $this->createUnit($unitData, self::UNIT_PIECES);

        $this->setPiecesAsDefaultUnit();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     * @param string|null $referenceName
     */
    private function createUnit(UnitData $unitData, $referenceName = null)
    {
        $unit = $this->unitFacade->create($unitData);
        if ($referenceName !== null) {
            $this->addReference($referenceName, $unit);
        }
    }

    private function setPiecesAsDefaultUnit(): void
    {
        $defaultUnit = $this->getReference(self::UNIT_PIECES);
        /** @var $defaultUnit \Shopsys\FrameworkBundle\Model\Product\Unit\Unit */
        $this->setting->set(Setting::DEFAULT_UNIT, $defaultUnit->getId());
    }
}

<?php

namespace Shopsys\ShopBundle\Model\Product\Unit;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Product\Unit\UnitFormType;
use Symfony\Component\Form\FormFactory;

class UnitInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Unit\UnitFacade
     */
    private $unitFacade;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    public function __construct(
        UnitGridFactory $unitGridFactory,
        UnitFacade $unitFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($unitGridFactory);
        $this->unitFacade = $unitFacade;
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
     * @return int
     */
    protected function createEntityAndGetId($unitData)
    {
        $unit = $this->unitFacade->create($unitData);

        return $unit->getId();
    }

    /**
     * @param int $unitId
     * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
     */
    protected function editEntity($unitId, $unitData)
    {
        $this->unitFacade->edit($unitId, $unitData);
    }

    /**
     * @param int|null $unitId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($unitId)
    {
        $unitData = new UnitData();

        if ($unitId !== null) {
            $unit = $this->unitFacade->getById((int)$unitId);
            $unitData->setFromEntity($unit);
        }

        return $this->formFactory->create(UnitFormType::class, $unitData);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return self::class;
    }
}

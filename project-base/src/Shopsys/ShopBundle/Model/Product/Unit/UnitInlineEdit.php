<?php

namespace Shopsys\ShopBundle\Model\Product\Unit;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Product\Unit\UnitFormType;
use Shopsys\ShopBundle\Model\Product\Unit\UnitData;
use Shopsys\ShopBundle\Model\Product\Unit\UnitFacade;
use Shopsys\ShopBundle\Model\Product\Unit\UnitGridFactory;
use Symfony\Component\Form\FormFactory;

class UnitInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Unit\UnitFacade
	 */
	private $unitFacade;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitGridFactory $unitGridFactory
	 * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitFacade $unitFacade
	 */
	public function __construct(
		FormFactory $formFactory,
		UnitGridFactory $unitGridFactory,
		UnitFacade $unitFacade
	) {
		$this->unitFacade = $unitFacade;

		parent::__construct($formFactory, $unitGridFactory);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
	 * @return int
	 */
	protected function createEntityAndGetId($unitData) {
		$unit = $this->unitFacade->create($unitData);

		return $unit->getId();
	}

	/**
	 * @param int $unitId
	 * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
	 */
	protected function editEntity($unitId, $unitData) {
		$this->unitFacade->edit($unitId, $unitData);
	}

	/**
	 * @param int|null $unitId
	 * @return \Shopsys\ShopBundle\Model\Product\Unit\UnitData
	 */
	protected function getFormDataObject($unitId = null) {
		$unitData = new UnitData();

		if ($unitId !== null) {
			$unitId = (int)$unitId;
			$unit = $this->unitFacade->getById($unitId);
			$unitData->setFromEntity($unit);
		}

		return $unitData;
	}

	/**
	 * @param int $rowId
	 * @return \Shopsys\ShopBundle\Form\Admin\Product\Unit\UnitFormType
	 */
	protected function getFormType($rowId) {
		return new UnitFormType();
	}

}

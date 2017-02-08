<?php

namespace SS6\ShopBundle\Model\Product\Unit;

use SS6\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use SS6\ShopBundle\Form\Admin\Product\Unit\UnitFormType;
use SS6\ShopBundle\Model\Product\Unit\UnitData;
use SS6\ShopBundle\Model\Product\Unit\UnitFacade;
use SS6\ShopBundle\Model\Product\Unit\UnitGridFactory;
use Symfony\Component\Form\FormFactory;

class UnitInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\UnitFacade
	 */
	private $unitFacade;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitGridFactory $unitGridFactory
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitFacade $unitFacade
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
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 * @return int
	 */
	protected function createEntityAndGetId($unitData) {
		$unit = $this->unitFacade->create($unitData);

		return $unit->getId();
	}

	/**
	 * @param int $unitId
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 */
	protected function editEntity($unitId, $unitData) {
		$this->unitFacade->edit($unitId, $unitData);
	}

	/**
	 * @param int|null $unitId
	 * @return \SS6\ShopBundle\Model\Product\Unit\UnitData
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
	 * @return \SS6\ShopBundle\Form\Admin\Product\Unit\UnitFormType
	 */
	protected function getFormType($rowId) {
		return new UnitFormType();
	}

}

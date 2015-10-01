<?php

namespace SS6\ShopBundle\Model\Product\Flag;

use SS6\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use SS6\ShopBundle\Form\Admin\Product\Flag\FlagFormType;
use SS6\ShopBundle\Model\Product\Flag\FlagData;
use SS6\ShopBundle\Model\Product\Flag\FlagFacade;
use SS6\ShopBundle\Model\Product\Flag\FlagGridFactory;
use Symfony\Component\Form\FormFactory;

class FlagInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\FlagFacade
	 */
	private $flagFacade;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagGridFactory $flagGridFactory
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagFacade $flagFacade
	 */
	public function __construct(
		FormFactory $formFactory,
		FlagGridFactory $flagGridFactory,
		FlagFacade $flagFacade
	) {
		$this->flagFacade = $flagFacade;

		parent::__construct($formFactory, $flagGridFactory);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagData $flagData
	 * @return int
	 */
	protected function createEntityAndGetId($flagData) {
		$flag = $this->flagFacade->create($flagData);

		return $flag->getId();
	}

	/**
	 * @param int $flagId
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagData $flagData
	 */
	protected function editEntity($flagId, $flagData) {
		$this->flagFacade->edit($flagId, $flagData);
	}

	/**
	 * @param int|null $flagId
	 * @return \SS6\ShopBundle\Model\Product\Flag\FlagData
	 */
	protected function getFormDataObject($flagId = null) {
		$flagData = new FlagData();

		if ($flagId !== null) {
			$flagId = (int)$flagId;
			$flag = $this->flagFacade->getById($flagId);
			$flagData->setFromEntity($flag);
		}

		return $flagData;
	}

	/**
	 * @param int $rowId
	 * @return \SS6\ShopBundle\Form\Admin\Product\Flag\FlagFormType
	 */
	protected function getFormType($rowId) {
		return new FlagFormType();
	}

}

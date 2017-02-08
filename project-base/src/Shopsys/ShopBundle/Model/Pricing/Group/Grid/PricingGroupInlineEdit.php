<?php

namespace SS6\ShopBundle\Model\Pricing\Group\Grid;

use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use SS6\ShopBundle\Form\Admin\Pricing\Group\PricingGroupFormType;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Symfony\Component\Form\FormFactory;

class PricingGroupInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	public function __construct(
		FormFactory $formFactory,
		PricingGroupGridFactory $pricingGroupGridFactory,
		PricingGroupFacade $pricingGroupFacade,
		SelectedDomain $selectedDomain
	) {
		$this->pricingGroupFacade = $pricingGroupFacade;
		$this->selectedDomain = $selectedDomain;

		parent::__construct($formFactory, $pricingGroupGridFactory);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
	 * @return int
	 */
	protected function createEntityAndGetId($pricingGroupData) {
		$pricingGroup = $this->pricingGroupFacade->create($pricingGroupData, $this->selectedDomain->getId());

		return $pricingGroup->getId();
	}

	/**
	 * @param int $pricingGroupId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
	 */
	protected function editEntity($pricingGroupId, $pricingGroupData) {
		$this->pricingGroupFacade->edit($pricingGroupId, $pricingGroupData);
	}

	/**
	 * @param int|null $pricingGroupId
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroupData
	 */
	protected function getFormDataObject($pricingGroupId = null) {
		$pricingGroupData = new PricingGroupData();

		if ($pricingGroupId !== null) {
			$pricingGroupId = (int)$pricingGroupId;
			$pricingGroup = $this->pricingGroupFacade->getById($pricingGroupId);
			$pricingGroupData->setFromEntity($pricingGroup);
		}

		return $pricingGroupData;
	}

	/**
	 * @param int $rowId
	 * @return \SS6\ShopBundle\Form\Admin\Pricing\Group\PricingGroupFormType
	 */
	protected function getFormType($rowId) {
		return new PricingGroupFormType();
	}
}

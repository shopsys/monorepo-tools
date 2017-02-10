<?php

namespace Shopsys\ShopBundle\Model\Pricing\Group\Grid;

use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Pricing\Group\PricingGroupFormType;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Symfony\Component\Form\FormFactory;

class PricingGroupInlineEdit extends AbstractGridInlineEdit {

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
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
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @return int
     */
    protected function createEntityAndGetId($pricingGroupData) {
        $pricingGroup = $this->pricingGroupFacade->create($pricingGroupData, $this->selectedDomain->getId());

        return $pricingGroup->getId();
    }

    /**
     * @param int $pricingGroupId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     */
    protected function editEntity($pricingGroupId, $pricingGroupData) {
        $this->pricingGroupFacade->edit($pricingGroupId, $pricingGroupData);
    }

    /**
     * @param int|null $pricingGroupId
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData
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
     * @return \Shopsys\ShopBundle\Form\Admin\Pricing\Group\PricingGroupFormType
     */
    protected function getFormType($rowId) {
        return new PricingGroupFormType();
    }
}

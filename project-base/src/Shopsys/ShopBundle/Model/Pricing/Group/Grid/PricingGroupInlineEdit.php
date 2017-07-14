<?php

namespace Shopsys\ShopBundle\Model\Pricing\Group\Grid;

use Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Pricing\Group\PricingGroupFormType;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Symfony\Component\Form\FormFactory;

class PricingGroupInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    public function __construct(
        PricingGroupGridFactory $pricingGroupGridFactory,
        PricingGroupFacade $pricingGroupFacade,
        AdminDomainTabsFacade $adminDomainTabsFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($pricingGroupGridFactory);
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @return int
     */
    protected function createEntityAndGetId($pricingGroupData)
    {
        $pricingGroup = $this->pricingGroupFacade->create($pricingGroupData, $this->adminDomainTabsFacade->getId());

        return $pricingGroup->getId();
    }

    /**
     * @param int $pricingGroupId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     */
    protected function editEntity($pricingGroupId, $pricingGroupData)
    {
        $this->pricingGroupFacade->edit($pricingGroupId, $pricingGroupData);
    }

    /**
     * @param int|null $pricingGroupId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($pricingGroupId)
    {
        $pricingGroupData = new PricingGroupData();

        if ($pricingGroupId !== null) {
            $pricingGroupId = (int)$pricingGroupId;
            $pricingGroup = $this->pricingGroupFacade->getById($pricingGroupId);
            $pricingGroupData->setFromEntity($pricingGroup);
        }

        return $this->formFactory->create(PricingGroupFormType::class, $pricingGroupData);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return 'shopsys.shop.pricing.group.grid.pricing_group_inline_edit';
    }
}

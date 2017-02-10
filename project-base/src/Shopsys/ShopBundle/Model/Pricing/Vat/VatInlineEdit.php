<?php

namespace Shopsys\ShopBundle\Model\Pricing\Vat;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Vat\VatFormType;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatGridFactory;
use Symfony\Component\Form\FormFactory;

class VatInlineEdit extends AbstractGridInlineEdit
{

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade
     */
    private $vatFacade;

    /**
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatGridFactory $vatGridFactory
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade $vatFacade
     */
    public function __construct(
        FormFactory $formFactory,
        VatGridFactory $vatGridFactory,
        VatFacade $vatFacade
    ) {
        $this->vatFacade = $vatFacade;

        parent::__construct($formFactory, $vatGridFactory);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatData $vatData
     * @return int
     */
    protected function createEntityAndGetId($vatData) {
        $vat = $this->vatFacade->create($vatData);

        return $vat->getId();
    }

    /**
     * @param int $vatId
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatData $vatData
     */
    protected function editEntity($vatId, $vatData) {
        $this->vatFacade->edit($vatId, $vatData);
    }

    /**
     * @param int|null $vatId
     * @return \Shopsys\ShopBundle\Model\Pricing\Vat\VatData
     */
    protected function getFormDataObject($vatId = null) {
        $vatData = new VatData();

        if ($vatId !== null) {
            $vatId = (int)$vatId;
            $vat = $this->vatFacade->getById($vatId);
            $vatData->setFromEntity($vat);
        }

        return $vatData;
    }

    /**
     * @param int $rowId
     * @return \Shopsys\ShopBundle\Form\Admin\Vat\VatFormType
     */
    protected function getFormType($rowId) {
        return new VatFormType($rowId === null ? VatFormType::SCENARIO_CREATE : VatFormType::SCENARIO_EDIT);
    }

}

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
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    public function __construct(
        VatGridFactory $vatGridFactory,
        VatFacade $vatFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($vatGridFactory);
        $this->vatFacade = $vatFacade;
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatData $vatData
     * @return int
     */
    protected function createEntityAndGetId($vatData)
    {
        $vat = $this->vatFacade->create($vatData);

        return $vat->getId();
    }

    /**
     * @param int $vatId
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatData $vatData
     */
    protected function editEntity($vatId, $vatData)
    {
        $this->vatFacade->edit($vatId, $vatData);
    }

    /**
     * @param int|null $vatId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($vatId)
    {
        $vatData = new VatData();

        if ($vatId !== null) {
            $vat = $this->vatFacade->getById((int)$vatId);
            $vatData->setFromEntity($vat);
        }

        return $this->formFactory->create(VatFormType::class, $vatData, [
            'scenario' => ($vatId === null ? VatFormType::SCENARIO_CREATE : VatFormType::SCENARIO_EDIT),
        ]);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return 'shopsys.shop.pricing.vat.vat_inline_edit';
    }
}

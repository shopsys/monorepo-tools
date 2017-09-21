<?php

namespace Shopsys\ShopBundle\Model\Order\PromoCode\Grid;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;
use Symfony\Component\Form\FormFactory;

class PromoCodeInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
     */
    private $promoCodeFacade;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    public function __construct(
        PromoCodeGridFactory $promoCodeGridFactory,
        PromoCodeFacade $promoCodeFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($promoCodeGridFactory);
        $this->promoCodeFacade = $promoCodeFacade;
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     * @return int
     */
    protected function createEntityAndGetId($promoCodeData)
    {
        $promoCode = $this->promoCodeFacade->create($promoCodeData);

        return $promoCode->getId();
    }

    /**
     * @param int $promoCodeId
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     */
    protected function editEntity($promoCodeId, $promoCodeData)
    {
        $this->promoCodeFacade->edit($promoCodeId, $promoCodeData);
    }

    /**
     * @param int|null $promoCodeId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($promoCodeId)
    {
        $promoCode = null;
        $promoCodeData = new PromoCodeData();

        if ($promoCodeId !== null) {
            $promoCode = $this->promoCodeFacade->getById((int)$promoCodeId);
            $promoCodeData->setFromEntity($promoCode);
        }

        return $this->formFactory->create(PromoCodeFormType::class, $promoCodeData, ['promo_code' => $promoCode]);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return 'shopsys.shop.order.promo_code.grid.promo_code_inline_edit';
    }
}

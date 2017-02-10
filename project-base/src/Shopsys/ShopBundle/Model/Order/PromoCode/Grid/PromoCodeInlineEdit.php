<?php

namespace Shopsys\ShopBundle\Model\Order\PromoCode\Grid;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\PromoCode\PromoCodeFormTypeFactory;
use Shopsys\ShopBundle\Model\Order\PromoCode\Grid\PromoCodeGridFactory;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;
use Symfony\Component\Form\FormFactory;

class PromoCodeInlineEdit extends AbstractGridInlineEdit {

    /**
     * @var \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
     */
    private $promoCodeFacade;

    /**
     * @var \Shopsys\ShopBundle\Form\Admin\PromoCode\PromoCodeFormTypeFactory
     */
    private $promoCodeFormTypeFactory;

    /**
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\Grid\PromoCodeGridFactory $promoCodeGridFactory
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade $promoCodeFacade
     */
    public function __construct(
        FormFactory $formFactory,
        PromoCodeGridFactory $promoCodeGridFactory,
        PromoCodeFacade $promoCodeFacade,
        PromoCodeFormTypeFactory $promoCodeFormTypeFactory
    ) {
        $this->promoCodeFacade = $promoCodeFacade;
        $this->promoCodeFormTypeFactory = $promoCodeFormTypeFactory;

        parent::__construct($formFactory, $promoCodeGridFactory);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     * @return int
     */
    protected function createEntityAndGetId($promoCodeData) {
        $promoCode = $this->promoCodeFacade->create($promoCodeData);

        return $promoCode->getId();
    }

    /**
     * @param int $promoCodeId
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     */
    protected function editEntity($promoCodeId, $promoCodeData) {
        $this->promoCodeFacade->edit($promoCodeId, $promoCodeData);
    }

    /**
     * @param int|null $promoCodeId
     * @return \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData
     */
    protected function getFormDataObject($promoCodeId = null) {
        $promoCodeData = new PromoCodeData();

        if ($promoCodeId !== null) {
            $promoCodeId = (int)$promoCodeId;
            $promoCode = $this->promoCodeFacade->getById($promoCodeId);
            $promoCodeData->setFromEntity($promoCode);
        }

        return $promoCodeData;
    }

    /**
     * @param int $promoCodeId
     * @return \Shopsys\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType
     */
    protected function getFormType($promoCodeId) {
        if ($promoCodeId !== null) {
            $promoCode = $this->promoCodeFacade->getById($promoCodeId);

            return $this->promoCodeFormTypeFactory->createForPromoCode($promoCode);
        }

        return $this->promoCodeFormTypeFactory->create();
    }

}

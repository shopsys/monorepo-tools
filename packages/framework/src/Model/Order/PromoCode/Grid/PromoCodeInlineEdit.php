<?php

namespace Shopsys\FrameworkBundle\Model\Order\PromoCode\Grid;

use Shopsys\FrameworkBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\FrameworkBundle\Form\Admin\PromoCode\PromoCodeFormType;
use Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeFacade;
use Symfony\Component\Form\FormFactoryInterface;

class PromoCodeInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeFacade
     */
    private $promoCodeFacade;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeDataFactoryInterface
     */
    private $promoCodeDataFactory;

    public function __construct(
        PromoCodeGridFactory $promoCodeGridFactory,
        PromoCodeFacade $promoCodeFacade,
        FormFactoryInterface $formFactory,
        PromoCodeDataFactoryInterface $promoCodeDataFactory
    ) {
        parent::__construct($promoCodeGridFactory);
        $this->promoCodeFacade = $promoCodeFacade;
        $this->formFactory = $formFactory;
        $this->promoCodeDataFactory = $promoCodeDataFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     * @return int
     */
    protected function createEntityAndGetId($promoCodeData)
    {
        $promoCode = $this->promoCodeFacade->create($promoCodeData);

        return $promoCode->getId();
    }

    /**
     * @param int $promoCodeId
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
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

        if ($promoCodeId !== null) {
            $promoCode = $this->promoCodeFacade->getById((int)$promoCodeId);
            $promoCodeData = $this->promoCodeDataFactory->createFromPromoCode($promoCode);
        } else {
            $promoCodeData = $this->promoCodeDataFactory->create();
        }

        return $this->formFactory->create(PromoCodeFormType::class, $promoCodeData, ['promo_code' => $promoCode]);
    }
}

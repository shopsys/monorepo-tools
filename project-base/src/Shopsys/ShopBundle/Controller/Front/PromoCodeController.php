<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PromoCodeController extends FrontBaseController
{
    const PROMO_CODE_PARAMETER = 'code';

    /**
     * @var \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
     */
    private $promoCodeFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
     */
    private $currentPromoCodeFacade;

    public function __construct(
        PromoCodeFacade $promoCodeFacade,
        CurrentPromoCodeFacade $currentPromoCodeFacade
    ) {
        $this->currentPromoCodeFacade = $currentPromoCodeFacade;
        $this->promoCodeFacade = $promoCodeFacade;
    }

    public function indexAction()
    {
        return $this->render('@ShopsysShop/Front/Content/Order/PromoCode/index.html.twig', [
            'validEnteredPromoCode' => $this->currentPromoCodeFacade->getValidEnteredPromoCodeOrNull(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function applyAction(Request $request)
    {
        $promoCode = $request->get(self::PROMO_CODE_PARAMETER);
        try {
            $this->currentPromoCodeFacade->setEnteredPromoCode($promoCode);
        } catch (\Shopsys\ShopBundle\Model\Order\PromoCode\Exception\InvalidPromoCodeException $ex) {
            return new JsonResponse([
                'result' => false,
                'message' => t('Discount code invalid. Check it, please.'),
            ]);
        }
        $this->getFlashMessageSender()->addSuccessFlash(t('Discount code added to order'));

        return new JsonResponse(['result' => true]);
    }

    public function removeAction()
    {
        $this->currentPromoCodeFacade->removeEnteredPromoCode();
        $this->getFlashMessageSender()->addSuccessFlash(t('Discount code removed from order'));

        return $this->redirectToRoute('front_cart');
    }
}

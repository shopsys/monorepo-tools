<?php

namespace Shopsys\ShopBundle\Controller\Front;

class FlashMessageController extends FrontBaseController
{
    public function indexAction()
    {
        $flashMessageBag = $this->get('shopsys.shop.component.flash_message.bag.front');
        /* @var $flashMessageBag \Shopsys\FrameworkBundle\Component\FlashMessage\Bag */

        return $this->render('@ShopsysShop/Front/Inline/FlashMessage/index.html.twig', [
            'errorMessages' => $flashMessageBag->getErrorMessages(),
            'infoMessages' => $flashMessageBag->getInfoMessages(),
            'successMessages' => $flashMessageBag->getSuccessMessages(),
        ]);
    }
}

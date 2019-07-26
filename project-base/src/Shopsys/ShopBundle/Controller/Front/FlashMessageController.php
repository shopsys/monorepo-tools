<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\Controller\Front;

class FlashMessageController extends FrontBaseController
{
    public function indexAction()
    {
        /** @var \Shopsys\FrameworkBundle\Component\FlashMessage\Bag $flashMessageBag */
        $flashMessageBag = $this->get('shopsys.shop.component.flash_message.bag.front');

        return $this->render('@ShopsysShop/Front/Inline/FlashMessage/index.html.twig', [
            'errorMessages' => $flashMessageBag->getErrorMessages(),
            'infoMessages' => $flashMessageBag->getInfoMessages(),
            'successMessages' => $flashMessageBag->getSuccessMessages(),
        ]);
    }
}

<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Shopsys\ShopBundle\Component\Controller\AdminBaseController;

class FlashMessageController extends AdminBaseController
{
    public function indexAction() {
        $flashMessageBag = $this->get('shopsys.shop.component.flash_message.bag.admin');
        /* @var $flashMessageBag \Shopsys\ShopBundle\Component\FlashMessage\Bag */

        return $this->render('@ShopsysShop/Admin/Inline/FlashMessage/index.html.twig', [
            'errorMessages' => $flashMessageBag->getErrorMessages(),
            'infoMessages' => $flashMessageBag->getInfoMessages(),
            'successMessages' => $flashMessageBag->getSuccessMessages(),
        ]);
    }
}

<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

class FlashMessageController extends AdminBaseController
{
    public function indexAction()
    {
        $flashMessageBag = $this->get('shopsys.shop.component.flash_message.bag.admin');
        /* @var $flashMessageBag \Shopsys\FrameworkBundle\Component\FlashMessage\Bag */

        return $this->render('@ShopsysFramework/Admin/Inline/FlashMessage/index.html.twig', [
            'errorMessages' => $flashMessageBag->getErrorMessages(),
            'infoMessages' => $flashMessageBag->getInfoMessages(),
            'successMessages' => $flashMessageBag->getSuccessMessages(),
        ]);
    }
}

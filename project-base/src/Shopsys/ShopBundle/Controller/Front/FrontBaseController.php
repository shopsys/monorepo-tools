<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontBaseController extends Controller
{
    /**
     * @return \Shopsys\FrameworkBundle\Component\FlashMessage\FlashMessageSender
     */
    public function getFlashMessageSender()
    {
        /** @var \Shopsys\FrameworkBundle\Component\FlashMessage\FlashMessageSender $flashMessageSender */
        $flashMessageSender = $this->get('shopsys.shop.component.flash_message.sender.front');
        return $flashMessageSender;
    }
}

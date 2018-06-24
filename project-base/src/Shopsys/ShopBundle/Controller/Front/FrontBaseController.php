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
        return $this->get('shopsys.shop.component.flash_message.sender.front');
    }
}

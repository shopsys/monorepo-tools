<?php

namespace Shopsys\ShopBundle\Component\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontBaseController extends Controller
{

    /**
     * @return \Shopsys\ShopBundle\Component\FlashMessage\FlashMessageSender
     */
    public function getFlashMessageSender() {
        return $this->get('shopsys.shop.component.flash_message.sender.front');
    }
}

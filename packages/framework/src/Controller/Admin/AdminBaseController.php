<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminBaseController extends Controller
{
    /**
     * @return \Shopsys\FrameworkBundle\Component\FlashMessage\FlashMessageSender
     */
    public function getFlashMessageSender()
    {
        /** @var \Shopsys\FrameworkBundle\Component\FlashMessage\FlashMessageSender $flashMessageSender */
        $flashMessageSender = $this->get('shopsys.shop.component.flash_message.sender.admin');
        return $flashMessageSender;
    }
}

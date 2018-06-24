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
        return $this->get('shopsys.shop.component.flash_message.sender.admin');
    }
}

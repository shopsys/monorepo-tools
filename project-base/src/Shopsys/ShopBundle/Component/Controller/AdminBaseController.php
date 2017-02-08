<?php

namespace SS6\ShopBundle\Component\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminBaseController extends Controller {

	/**
	 * @return \SS6\ShopBundle\Component\FlashMessage\FlashMessageSender
	 */
	public function getFlashMessageSender() {
		return $this->get('ss6.shop.component.flash_message.sender.admin');
	}
}

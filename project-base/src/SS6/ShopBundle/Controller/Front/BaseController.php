<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller {

	/**
	 * @return \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender
	 */
	public function getFlashMessageSender() {
		return $this->get('ss6.shop.flash_message.sender.front');
	}
}

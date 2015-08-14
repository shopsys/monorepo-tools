<?php

namespace SS6\ShopBundle\Component\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontBaseController extends Controller {

	/**
	 * @return \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender
	 */
	public function getFlashMessageSender() {
		return $this->get('ss6.shop.flash_message.sender.front');
	}
}

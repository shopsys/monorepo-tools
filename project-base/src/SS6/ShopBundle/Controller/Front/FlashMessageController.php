<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FlashMessageController extends Controller {

	public function indexAction() {
		$flashMessage = $this->get('ss6.shop.flash_message.front');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		return $this->render('@SS6Shop/Front/Inline/FlashMessage/index.html.twig', array(
			'errorMessages' => $flashMessage->getErrorMessages(),
			'infoMessages' => $flashMessage->getInfoMessages(),
			'successMessages' => $flashMessage->getSuccessMessages(),
		));
	}

}

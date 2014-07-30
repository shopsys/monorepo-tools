<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FlashMessageController extends Controller {

	public function indexAction() {
		$flashMessageBag = $this->get('ss6.shop.flash_message.bag.front');
		/* @var $flashMessageBag \SS6\ShopBundle\Model\FlashMessage\Bag */

		return $this->render('@SS6Shop/Front/Inline/FlashMessage/index.html.twig', array(
			'errorMessages' => $flashMessageBag->getErrorMessages(),
			'infoMessages' => $flashMessageBag->getInfoMessages(),
			'successMessages' => $flashMessageBag->getSuccessMessages(),
		));
	}

}

<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Controller\Front\BaseController;

class FlashMessageController extends BaseController {

	public function indexAction() {
		$flashMessageBag = $this->get('ss6.shop.flash_message.bag.front');
		/* @var $flashMessageBag \SS6\ShopBundle\Model\FlashMessage\Bag */

		return $this->render('@SS6Shop/Front/Inline/FlashMessage/index.html.twig', [
			'errorMessages' => $flashMessageBag->getErrorMessages(),
			'infoMessages' => $flashMessageBag->getInfoMessages(),
			'successMessages' => $flashMessageBag->getSuccessMessages(),
		]);
	}

}

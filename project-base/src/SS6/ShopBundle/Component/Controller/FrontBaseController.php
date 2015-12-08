<?php

namespace SS6\ShopBundle\Component\Controller;

use Closure;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontBaseController extends Controller {

	/**
	 * @return \SS6\ShopBundle\Component\FlashMessage\FlashMessageSender
	 */
	public function getFlashMessageSender() {
		return $this->get('ss6.shop.component.flash_message.sender.front');
	}

	/**
	 * @param \Closure
	 */
	public function transactional(Closure $closure) {
		$em = $this->get(EntityManager::class);
		/* @var $em \Doctrine\ORM\EntityManager */

		return $em->transactional($closure);
	}
}

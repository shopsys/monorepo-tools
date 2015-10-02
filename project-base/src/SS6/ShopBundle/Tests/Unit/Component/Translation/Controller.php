<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Translation;

use JMS\TranslationBundle\Annotation\Ignore;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class Controller extends BaseController {

	public function indexAction() {
		$flashMessageSender = $this->get('ss6.shop.component.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Component\FlashMessage\FlashMessageSender */
		$translator = $this->get('translator');
		/* @var $translator \Symfony\Component\Translation\TranslatorInterface */

		$flashMessageSender->addErrorFlash('ErrorFlash');
		$flashMessageSender->addInfoFlash('InfoFlash');
		$flashMessageSender->addSuccessFlash('SuccessFlash');
		$flashMessageSender->addErrorFlashTwig('ErrorFlashTwig');
		$flashMessageSender->addInfoFlashTwig('InfoFlashTwig');
		$flashMessageSender->addSuccessFlashTwig('SuccessFlashTwig');

		$flashMessageSender->addSuccessFlashTwig('SuccessFlashTwig2', ['param' => 'value']);

		$translator->trans('trans test');

		$translator->transChoice('transChoice test', 5);

		$translator->trans('trans test with domain', [], 'testDomain');
		$translator->transChoice('transChoice test with domain', 5, [], 'testDomain');

		/** @Ignore */
		$translator->trans('ignored');
	}

}

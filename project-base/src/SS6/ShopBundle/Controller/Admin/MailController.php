<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Order\Status\AllMailTemplatesFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MailController extends Controller {

	/**
	 * @Route("/mail/template/")
	 */
	public function templateAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$mailTemplateFacade = $this->get('ss6.shop.mail.mail_template_facade');
		/* @var $mailTemplateFacade \SS6\ShopBundle\Model\Mail\MailTemplateFacade */

		$allMailTemplatesData = $mailTemplateFacade->getAllMailTemplatesData();

		$form = $this->createForm(new AllMailTemplatesFormType());

		$form->setData($allMailTemplatesData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$mailTemplateFacade->saveMailTemplatesData($allMailTemplatesData->getAllTemplates());

			$flashMessageSender->addSuccess('Nastavení šablony e-mailu bylo upraveno');
			return $this->redirect($this->generateUrl('admin_mail_template'));
		}

		return $this->render('@SS6Shop/Admin/Content/Mail/template.html.twig', array(
			'form' => $form->createView(),
			'orderStatusesIndexedById' => $mailTemplateFacade->getAllIndexedById(),
		));
	}

}

<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Mail\MailTemplateFormType;
use SS6\ShopBundle\Model\Mail\MailTemplateData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MailController extends Controller {

	/**
	 * @Route("/mail/template/")
	 */
	public function templateAction(Request $request) {
		$flashMessageText = $this->get('ss6.shop.flash_message.text_sender.admin');
		/* @var $flashMessageText \SS6\ShopBundle\Model\FlashMessage\TextSender */
		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */
		$orderMailFacade = $this->get('ss6.shop.order.order_mail_facade');
		/* @var $orderMailFacade \SS6\ShopBundle\Model\Order\Mail\OrderMailFacade */

		$defaultOrderStatus = $orderStatusRepository->getDefault();
		$mailTemplate = $orderMailFacade->getMailTemplateByStatus($defaultOrderStatus);


		$form = $this->createForm(new MailTemplateFormType());

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setFromEntity($mailTemplate);

		$form->setData($mailTemplateData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$mailTemplateFacade = $this->get('ss6.shop.mail.mail_template_facade');
			/* @var $mailTemplateFacade \SS6\ShopBundle\Model\Mail\MailTemplateFacade */
			$mailTemplateFacade->edit($mailTemplate, $mailTemplateData);

			$flashMessageText->addSuccess('Nastavení šablony e-mailu bylo upraveno');
			return $this->redirect($this->generateUrl('admin_mail_template'));
		}

		return $this->render('@SS6Shop/Admin/Content/Mail/template.html.twig', array(
			'form' => $form->createView(),
		));
	}

}

<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Order\Status\OrderStatusMailTemplatesFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MailController extends Controller {

	/**
	 * @Route("/mail/template/")
	 */
	public function templateAction(Request $request) {
		$flashMessageText = $this->get('ss6.shop.flash_message.text_sender.admin');
		/* @var $flashMessageText \SS6\ShopBundle\Model\FlashMessage\TextSender */
		$mailTemplateFacade = $this->get('ss6.shop.mail.mail_template_facade');
		/* @var $mailTemplateFacade \SS6\ShopBundle\Model\Mail\MailTemplateFacade */
		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */

		$orderStatusesIndexedById = $orderStatusRepository->getAllIndexedById();
		$orderStatusMailTemplatesData = $mailTemplateFacade->getOrderStatusMailTemplatesData();

		$form = $this->createForm(new OrderStatusMailTemplatesFormType());

		$form->setData($orderStatusMailTemplatesData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$mailTemplateFacade->saveMailTemplatesData($orderStatusMailTemplatesData->getTemplates());

			$flashMessageText->addSuccess('Nastavení šablon e-mailů bylo upraveno');
			return $this->redirect($this->generateUrl('admin_mail_template'));
		}

		return $this->render('@SS6Shop/Admin/Content/Mail/template.html.twig', array(
			'form' => $form->createView(),
			'orderStatusesIndexedById' => $orderStatusesIndexedById,
		));
	}

}
<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SS6\ShopBundle\Form\Admin\Mail\MailTemplatesAllStatusesFormType;
use SS6\ShopBundle\Model\Mail\MailTemplateData;

class MailController extends Controller {

	/**
	 * @Route("/mail/template/")
	 */
	public function templateAction(Request $request) {
		$flashMessageText = $this->get('ss6.shop.flash_message.text_sender.admin');
		/* @var $flashMessageText \SS6\ShopBundle\Model\FlashMessage\TextSender */
		$orderMailFacade = $this->get('ss6.shop.order.order_mail_facade');
		/* @var $orderMailFacade \SS6\ShopBundle\Model\Order\Mail\OrderMailFacade */
		$mailTemplateFacade = $this->get('ss6.shop.mail.mail_template_facade');
		/* @var $mailTemplateFacade \SS6\ShopBundle\Model\Mail\MailTemplateFacade */

		$orderStatusNames = $orderMailFacade->getNamesByMailTemplateName();
		$mailTemplateFacade->prepareAllTemplates();
		$mailTemplateNames = array_keys($orderStatusNames);

		$form = $this->createForm(new MailTemplatesAllStatusesFormType($mailTemplateNames));

		$formData = array();
		foreach ($orderMailFacade->getAllOrderStatusMailTemplates() as $mailTemplate) {
			$mailTemplateData = new MailTemplateData();
			$mailTemplateData->setFromEntity($mailTemplate);
			$formData[$mailTemplate->getName()] = $mailTemplateData;
		}

		$form->setData($formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			foreach ($orderMailFacade->getAllOrderStatusMailTemplates() as $mailTemplate) {
				$mailTemplateFacade->edit($mailTemplate, $formData[$mailTemplate->getName()]);
			}
			$flashMessageText->addSuccess('Nastavení šablon e-mailů bylo upraveno');
			return $this->redirect($this->generateUrl('admin_mail_template'));
		}

		return $this->render('@SS6Shop/Admin/Content/Mail/template.html.twig', array(
			'form' => $form->createView(),
			'orderStatusNames' => $orderStatusNames,
		));
	}

}
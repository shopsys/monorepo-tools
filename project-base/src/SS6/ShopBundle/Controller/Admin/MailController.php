<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Mail\MailSettingFormType;
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

		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */
		$selectedDomain = $this->get('ss6.shop.domain.selected_domain');
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */

		$customerMailService = $this->get('ss6.shop.customer.mail.customer_mail_service');
		/* @var $customerMailService \SS6\ShopBundle\Model\Customer\Mail\CustomerMailService */
		$orderMailService = $this->get('ss6.shop.order.order_mail_service');
		/* @var $orderMailService \SS6\ShopBundle\Model\Order\Mail\OrderMailService */

		$allMailTemplatesData = $mailTemplateFacade->getAllMailTemplatesDataByDomainId($selectedDomain->getId());

		$form = $this->createForm(new AllMailTemplatesFormType());

		$form->setData($allMailTemplatesData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$mailTemplateFacade->saveMailTemplatesData($allMailTemplatesData->getAllTemplates(), $selectedDomain->getId());

			$flashMessageSender->addSuccess('Nastavení šablony e-mailu bylo upraveno');
			return $this->redirect($this->generateUrl('admin_mail_template'));
		}

		$orderStatusesTemplateVariables = $orderMailService->getOrderStatusesTemplateVariables();
		$registrationTemplateVariables = $customerMailService->getRegistrationTemplateVariables();

		return $this->render('@SS6Shop/Admin/Content/Mail/template.html.twig', array(
			'form' => $form->createView(),
			'orderStatusesIndexedById' => $mailTemplateFacade->getAllIndexedById(),
			'orderStatusVariables' => $orderStatusesTemplateVariables,
			'registrationVariables' => $registrationTemplateVariables,
		));
	}

	/**
	 * @Route("/mail/setting/")
	 */
	public function settingAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$mailSettingFacade = $this->get('ss6.shop.mail.setting.mail_setting_facade');
		/* @var $mailSettingFacade \SS6\ShopBundle\Model\Mail\Setting\MailSettingFacade */

		$form = $this->createForm(new MailSettingFormType());

		$mailSettingData = array();

		if(!$form->isSubmitted()) {
			$mailSettingData['email'] = $mailSettingFacade->getMainAdminMail();
			$mailSettingData['name'] = $mailSettingFacade->getMainAdminMailName();
		}

		$form->setData($mailSettingData);
		$form->handleRequest($request);

		if($form->isValid()) {
			$mailSettingData = $form->getData();

			$mailSettingFacade->setMainAdminMail($mailSettingData['email']);
			$mailSettingFacade->setMainAdminMailNAme($mailSettingData['name']);

			$flashMessageSender->addSuccess('Nastavení emailů bylo upraveno.');
		}

		return $this->render('@SS6Shop/Admin/Content/Mail/setting.html.twig', array(
			'form' =>$form->createView(),
		));
	}

}

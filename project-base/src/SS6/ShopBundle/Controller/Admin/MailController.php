<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Mail\MailSettingFormType;
use SS6\ShopBundle\Form\Admin\Order\Status\AllMailTemplatesFormType;
use SS6\ShopBundle\Model\Customer\Mail\CustomerMailService;
use SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMail;
use SS6\ShopBundle\Model\Order\Mail\OrderMailService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MailController extends Controller {

	/**
	 * @return array
	 */
	private function getOrderStatusVariablesLabels() {
		$translator = $this->get('translator');
		/* @var $translator \Symfony\Component\Translation\TranslatorInterface */

		return [
			OrderMailService::VARIABLE_NUMBER  => $translator->trans('Číslo objednávky'),
			OrderMailService::VARIABLE_DATE => $translator->trans('Datum a čas vytvoření objednávky'),
			OrderMailService::VARIABLE_URL => $translator->trans('URL adresa e-shopu'),
			OrderMailService::VARIABLE_TRANSPORT => $translator->trans('Název zvolené dopravy'),
			OrderMailService::VARIABLE_PAYMENT => $translator->trans('Název zvolené platby'),
			OrderMailService::VARIABLE_TOTAL_PRICE => $translator->trans('Celková cena za objednávku (s DPH)'),
			OrderMailService::VARIABLE_BILLING_ADDRESS => $translator->trans(
				'Fakturační adresa - jméno, příjmení, firma, ič, dič a fakt. adresa'
			),
			OrderMailService::VARIABLE_DELIVERY_ADDRESS => $translator->trans('Dodací adresa'),
			OrderMailService::VARIABLE_NOTE  => $translator->trans('Poznámka'),
			OrderMailService::VARIABLE_PRODUCTS => $translator->trans(
				'Seznam zboží v objednávce (název, počet kusů, cena za kus s DPH, celková cena za položku s DPH)'
			),
			OrderMailService::VARIABLE_ORDER_DETAIL_URL => $translator->trans('URL adresa detailu objednávky'),
			OrderMailService::VARIABLE_TRANSPORT_INSTRUCTIONS => $translator->trans('Pokyny k dopravě'),
			OrderMailService::VARIABLE_PAYMENT_INSTRUCTIONS => $translator->trans('Pokyny k platbě'),
		];
	}

	/**
	 * @return array
	 */
	private function getRegistrationVariablesLabels() {
		$translator = $this->get('translator');
		/* @var $translator \Symfony\Component\Translation\TranslatorInterface */

		return [
			CustomerMailService::VARIABLE_FIRST_NAME => $translator->trans('Jméno'),
			CustomerMailService::VARIABLE_LAST_NAME => $translator->trans('Příjmení'),
			CustomerMailService::VARIABLE_EMAIL => $translator->trans('Email'),
			CustomerMailService::VARIABLE_URL => $translator->trans('URL adresa e-shopu'),
			CustomerMailService::VARIABLE_LOGIN_PAGE => $translator->trans('Odkaz na stránku s přihlášením'),
		];
	}

	/**
	 * @return array
	 */
	private function getResetPasswordVariablesLabels() {
		$translator = $this->get('translator');
		/* @var $translator \Symfony\Component\Translation\TranslatorInterface */

		return [
			ResetPasswordMail::VARIABLE_EMAIL => $translator->trans('Email'),
			ResetPasswordMail::VARIABLE_NEW_PASSWORD_URL => $translator->trans('URL adresa pro nastavení nového hesla'),
		];
	}

	/**
	 * @Route("/mail/template/")
	 */
	public function templateAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$mailTemplateFacade = $this->get('ss6.shop.mail.mail_template_facade');
		/* @var $mailTemplateFacade \SS6\ShopBundle\Model\Mail\MailTemplateFacade */
		$selectedDomain = $this->get('ss6.shop.domain.selected_domain');
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */
		$customerMailService = $this->get('ss6.shop.customer.mail.customer_mail_service');
		/* @var $customerMailService \SS6\ShopBundle\Model\Customer\Mail\CustomerMailService */
		$orderMailService = $this->get('ss6.shop.order.order_mail_service');
		/* @var $orderMailService \SS6\ShopBundle\Model\Order\Mail\OrderMailService */
		$resetPasswordMail = $this->get('ss6.shop.customer.mail.reset_password_mail');
		/* @var $resetPasswordMail \SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMail */

		$allMailTemplatesData = $mailTemplateFacade->getAllMailTemplatesDataByDomainId($selectedDomain->getId());

		$form = $this->createForm(new AllMailTemplatesFormType());

		$form->setData($allMailTemplatesData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$mailTemplateFacade->saveMailTemplatesData($allMailTemplatesData->getAllTemplates(), $allMailTemplatesData->domainId);

			$flashMessageSender->addSuccessFlash('Nastavení šablony e-mailu bylo upraveno');
			return $this->redirect($this->generateUrl('admin_mail_template'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlash('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$orderStatusesTemplateVariables = $orderMailService->getTemplateVariables();
		$registrationTemplateVariables = $customerMailService->getTemplateVariables();
		$resetPasswordTemplateVariables = array_unique(array_merge(
			$resetPasswordMail->getBodyVariables(),
			$resetPasswordMail->getSubjectVariables()
		));
		$resetPasswordTemplateRequiredVariables = array_unique(array_merge(
			$resetPasswordMail->getRequiredBodyVariables(),
			$resetPasswordMail->getRequiredSubjectVariables()
		));

		return $this->render('@SS6Shop/Admin/Content/Mail/template.html.twig', [
			'form' => $form->createView(),
			'orderStatusesIndexedById' => $mailTemplateFacade->getAllIndexedById(),
			'orderStatusVariables' => $orderStatusesTemplateVariables,
			'orderStatusVariablesLabels' => $this->getOrderStatusVariablesLabels(),
			'registrationVariables' => $registrationTemplateVariables,
			'registrationVariablesLabels' => $this->getRegistrationVariablesLabels(),
			'resetPasswordVariables' => $resetPasswordTemplateVariables,
			'resetPasswordRequiredVariables' => $resetPasswordTemplateRequiredVariables,
			'resetPasswordVariablesLabels' => $this->getResetPasswordVariablesLabels(),
		]);
	}

	/**
	 * @Route("/mail/setting/")
	 */
	public function settingAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$mailSettingFacade = $this->get('ss6.shop.mail.setting.mail_setting_facade');
		/* @var $mailSettingFacade \SS6\ShopBundle\Model\Mail\Setting\MailSettingFacade */
		$selectedDomain = $this->get('ss6.shop.domain.selected_domain');
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */
		$selectedDomainId = $selectedDomain->getId();

		$form = $this->createForm(new MailSettingFormType());

		$mailSettingData = [];

		if (!$form->isSubmitted()) {
			$mailSettingData['email'] = $mailSettingFacade->getMainAdminMail($selectedDomainId);
			$mailSettingData['name'] = $mailSettingFacade->getMainAdminMailName($selectedDomainId);
		}

		$form->setData($mailSettingData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$mailSettingData = $form->getData();

			$mailSettingFacade->setMainAdminMail($mailSettingData['email'], $selectedDomainId);
			$mailSettingFacade->setMainAdminMailName($mailSettingData['name'], $selectedDomainId);

			$flashMessageSender->addSuccessFlash('Nastavení emailů bylo upraveno.');
		}

		return $this->render('@SS6Shop/Admin/Content/Mail/setting.html.twig', [
			'form' => $form->createView(),
		]);
	}

}

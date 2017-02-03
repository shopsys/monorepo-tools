<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Form\Admin\Mail\AllMailTemplatesFormTypeFactory;
use SS6\ShopBundle\Form\Admin\Mail\MailSettingFormType;
use SS6\ShopBundle\Model\Customer\Mail\RegistrationMailService;
use SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMail;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MailTemplateFacade;
use SS6\ShopBundle\Model\Mail\Setting\MailSettingFacade;
use SS6\ShopBundle\Model\Order\Mail\OrderMailService;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusFacade;
use Symfony\Component\HttpFoundation\Request;

class MailController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Mail\AllMailTemplatesFormTypeFactory
	 */
	private $allMailTemplatesFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\Mail\RegistrationMailService
	 */
	private $registrationMailService;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMail
	 */
	private $resetPasswordMail;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateFacade
	 */
	private $mailTemplateFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\Setting\MailSettingFacade
	 */
	private $mailSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Mail\OrderMailService
	 */
	private $orderMailService;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade
	 */
	private $orderStatusFacade;

	public function __construct(
		AllMailTemplatesFormTypeFactory $allMailTemplatesFormTypeFactory,
		ResetPasswordMail $resetPasswordMail,
		OrderMailService $orderMailService,
		RegistrationMailService $registrationMailService,
		SelectedDomain $selectedDomain,
		MailTemplateFacade $mailTemplateFacade,
		MailSettingFacade $mailSettingFacade,
		OrderStatusFacade $orderStatusFacade
	) {
		$this->allMailTemplatesFormTypeFactory = $allMailTemplatesFormTypeFactory;
		$this->resetPasswordMail = $resetPasswordMail;
		$this->orderMailService = $orderMailService;
		$this->registrationMailService = $registrationMailService;
		$this->selectedDomain = $selectedDomain;
		$this->mailTemplateFacade = $mailTemplateFacade;
		$this->mailSettingFacade = $mailSettingFacade;
		$this->orderStatusFacade = $orderStatusFacade;
	}

	/**
	 * @return array
	 */
	private function getOrderStatusVariablesLabels() {
		return [
			OrderMailService::VARIABLE_NUMBER => t('Číslo objednávky'),
			OrderMailService::VARIABLE_DATE => t('Datum a čas vytvoření objednávky'),
			OrderMailService::VARIABLE_URL => t('URL adresa e-shopu'),
			OrderMailService::VARIABLE_TRANSPORT => t('Název zvolené dopravy'),
			OrderMailService::VARIABLE_PAYMENT => t('Název zvolené platby'),
			OrderMailService::VARIABLE_TOTAL_PRICE => t('Celková cena za objednávku (s DPH)'),
			OrderMailService::VARIABLE_BILLING_ADDRESS => t(
				'Fakturační adresa - jméno, příjmení, firma, ič, dič a fakt. adresa'
			),
			OrderMailService::VARIABLE_DELIVERY_ADDRESS => t('Dodací adresa'),
			OrderMailService::VARIABLE_NOTE => t('Poznámka'),
			OrderMailService::VARIABLE_PRODUCTS => t(
				'Seznam zboží v objednávce (název, množství, cena za jednotku s DPH, celková cena za položku s DPH)'
			),
			OrderMailService::VARIABLE_ORDER_DETAIL_URL => t('URL adresa detailu objednávky'),
			OrderMailService::VARIABLE_TRANSPORT_INSTRUCTIONS => t('Pokyny k dopravě'),
			OrderMailService::VARIABLE_PAYMENT_INSTRUCTIONS => t('Pokyny k platbě'),
		];
	}

	/**
	 * @return array
	 */
	private function getRegistrationVariablesLabels() {
		return [
			RegistrationMailService::VARIABLE_FIRST_NAME => t('Jméno'),
			RegistrationMailService::VARIABLE_LAST_NAME => t('Příjmení'),
			RegistrationMailService::VARIABLE_EMAIL => t('E-mail'),
			RegistrationMailService::VARIABLE_URL => t('URL adresa e-shopu'),
			RegistrationMailService::VARIABLE_LOGIN_PAGE => t('Odkaz na stránku s přihlášením'),
		];
	}

	/**
	 * @return array
	 */
	private function getResetPasswordVariablesLabels() {
		return [
			ResetPasswordMail::VARIABLE_EMAIL => t('E-mail'),
			ResetPasswordMail::VARIABLE_NEW_PASSWORD_URL => t('URL adresa pro nastavení nového hesla'),
		];
	}

	/**
	 * @Route("/mail/template/")
	 */
	public function templateAction(Request $request) {
		$form = $this->createForm($this->allMailTemplatesFormTypeFactory->create());

		$allMailTemplatesData = $this->mailTemplateFacade->getAllMailTemplatesDataByDomainId($this->selectedDomain->getId());

		$form->setData($allMailTemplatesData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->mailTemplateFacade->saveMailTemplatesData(
				$allMailTemplatesData->getAllTemplates(),
				$allMailTemplatesData->domainId
			);

			$this->getFlashMessageSender()->addSuccessFlash(t('Nastavení šablony e-mailu bylo upraveno'));

			return $this->redirectToRoute('admin_mail_template');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		$templateParameters = $this->getTemplateParameters();
		$templateParameters['form'] = $form->createView();

		return $this->render('@SS6Shop/Admin/Content/Mail/template.html.twig', $templateParameters);
	}

	/**
	 * @Route("/mail/setting/")
	 */
	public function settingAction(Request $request) {
		$selectedDomainId = $this->selectedDomain->getId();

		$form = $this->createForm(new MailSettingFormType());

		$mailSettingData = [];

		if (!$form->isSubmitted()) {
			$mailSettingData['email'] = $this->mailSettingFacade->getMainAdminMail($selectedDomainId);
			$mailSettingData['name'] = $this->mailSettingFacade->getMainAdminMailName($selectedDomainId);
		}

		$form->setData($mailSettingData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$mailSettingData = $form->getData();

			$this->mailSettingFacade->setMainAdminMail($mailSettingData['email'], $selectedDomainId);
			$this->mailSettingFacade->setMainAdminMailName($mailSettingData['name'], $selectedDomainId);

			$this->getFlashMessageSender()->addSuccessFlash(t('Nastavení emailů bylo upraveno.'));
		}

		return $this->render('@SS6Shop/Admin/Content/Mail/setting.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @return array
	 */
	private function getTemplateParameters() {
		$orderStatusesTemplateVariables = $this->orderMailService->getTemplateVariables();
		$registrationTemplateVariables = $this->registrationMailService->getTemplateVariables();
		$resetPasswordTemplateVariables = array_unique(array_merge(
			$this->resetPasswordMail->getBodyVariables(),
			$this->resetPasswordMail->getSubjectVariables()
		));
		$resetPasswordTemplateRequiredVariables = array_unique(array_merge(
			$this->resetPasswordMail->getRequiredBodyVariables(),
			$this->resetPasswordMail->getRequiredSubjectVariables()
		));

		$selectedDomainId = $this->selectedDomain->getId();
		$orderStatusMailTemplatesByOrderStatusId = $this->mailTemplateFacade->getOrderStatusMailTemplatesIndexedByOrderStatusId(
			$selectedDomainId
		);
		$registrationMailTemplate = $this->mailTemplateFacade->get(
			MailTemplate::REGISTRATION_CONFIRM_NAME,
			$selectedDomainId
		);
		$resetPasswordMailTemplate = $this->mailTemplateFacade->get(
			MailTemplate::RESET_PASSWORD_NAME,
			$selectedDomainId
		);

		return [
			'orderStatusesIndexedById' => $this->orderStatusFacade->getAllIndexedById(),
			'orderStatusMailTemplatesByOrderStatusId' => $orderStatusMailTemplatesByOrderStatusId,
			'orderStatusVariables' => $orderStatusesTemplateVariables,
			'orderStatusVariablesLabels' => $this->getOrderStatusVariablesLabels(),
			'registrationMailTemplate' => $registrationMailTemplate,
			'registrationVariables' => $registrationTemplateVariables,
			'registrationVariablesLabels' => $this->getRegistrationVariablesLabels(),
			'resetPasswordMailTemplate' => $resetPasswordMailTemplate,
			'resetPasswordRequiredVariables' => $resetPasswordTemplateRequiredVariables,
			'resetPasswordVariables' => $resetPasswordTemplateVariables,
			'resetPasswordVariablesLabels' => $this->getResetPasswordVariablesLabels(),
			'TYPE_NEW' => OrderStatus::TYPE_NEW,
		];
	}

}

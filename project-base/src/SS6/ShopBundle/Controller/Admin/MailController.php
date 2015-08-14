<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\Admin\Mail\AllMailTemplatesFormTypeFactory;
use SS6\ShopBundle\Form\Admin\Mail\MailSettingFormType;
use SS6\ShopBundle\Model\Customer\Mail\CustomerMailService;
use SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMail;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Mail\MailTemplateFacade;
use SS6\ShopBundle\Model\Mail\Setting\MailSettingFacade;
use SS6\ShopBundle\Model\Order\Mail\OrderMailService;
use Symfony\Component\HttpFoundation\Request;

class MailController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Mail\AllMailTemplatesFormTypeFactory
	 */
	private $allMailTemplatesFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\Mail\CustomerMailService
	 */
	private $customerMailService;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMail
	 */
	private $resetPasswordMail;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
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
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		Translator $translator,
		AllMailTemplatesFormTypeFactory $allMailTemplatesFormTypeFactory,
		ResetPasswordMail $resetPasswordMail,
		OrderMailService $orderMailService,
		CustomerMailService $customerMailService,
		SelectedDomain $selectedDomain,
		MailTemplateFacade $mailTemplateFacade,
		MailSettingFacade $mailSettingFacade
	) {
		$this->translator = $translator;
		$this->allMailTemplatesFormTypeFactory = $allMailTemplatesFormTypeFactory;
		$this->resetPasswordMail = $resetPasswordMail;
		$this->orderMailService = $orderMailService;
		$this->customerMailService = $customerMailService;
		$this->selectedDomain = $selectedDomain;
		$this->mailTemplateFacade = $mailTemplateFacade;
		$this->mailSettingFacade = $mailSettingFacade;
	}

	/**
	 * @return array
	 */
	private function getOrderStatusVariablesLabels() {
		return [
			OrderMailService::VARIABLE_NUMBER => $this->translator->trans('Číslo objednávky'),
			OrderMailService::VARIABLE_DATE => $this->translator->trans('Datum a čas vytvoření objednávky'),
			OrderMailService::VARIABLE_URL => $this->translator->trans('URL adresa e-shopu'),
			OrderMailService::VARIABLE_TRANSPORT => $this->translator->trans('Název zvolené dopravy'),
			OrderMailService::VARIABLE_PAYMENT => $this->translator->trans('Název zvolené platby'),
			OrderMailService::VARIABLE_TOTAL_PRICE => $this->translator->trans('Celková cena za objednávku (s DPH)'),
			OrderMailService::VARIABLE_BILLING_ADDRESS => $this->translator->trans(
				'Fakturační adresa - jméno, příjmení, firma, ič, dič a fakt. adresa'
			),
			OrderMailService::VARIABLE_DELIVERY_ADDRESS => $this->translator->trans('Dodací adresa'),
			OrderMailService::VARIABLE_NOTE => $this->translator->trans('Poznámka'),
			OrderMailService::VARIABLE_PRODUCTS => $this->translator->trans(
				'Seznam zboží v objednávce (název, počet kusů, cena za kus s DPH, celková cena za položku s DPH)'
			),
			OrderMailService::VARIABLE_ORDER_DETAIL_URL => $this->translator->trans('URL adresa detailu objednávky'),
			OrderMailService::VARIABLE_TRANSPORT_INSTRUCTIONS => $this->translator->trans('Pokyny k dopravě'),
			OrderMailService::VARIABLE_PAYMENT_INSTRUCTIONS => $this->translator->trans('Pokyny k platbě'),
		];
	}

	/**
	 * @return array
	 */
	private function getRegistrationVariablesLabels() {
		return [
			CustomerMailService::VARIABLE_FIRST_NAME => $this->translator->trans('Jméno'),
			CustomerMailService::VARIABLE_LAST_NAME => $this->translator->trans('Příjmení'),
			CustomerMailService::VARIABLE_EMAIL => $this->translator->trans('Email'),
			CustomerMailService::VARIABLE_URL => $this->translator->trans('URL adresa e-shopu'),
			CustomerMailService::VARIABLE_LOGIN_PAGE => $this->translator->trans('Odkaz na stránku s přihlášením'),
		];
	}

	/**
	 * @return array
	 */
	private function getResetPasswordVariablesLabels() {
		return [
			ResetPasswordMail::VARIABLE_EMAIL => $this->translator->trans('Email'),
			ResetPasswordMail::VARIABLE_NEW_PASSWORD_URL => $this->translator->trans('URL adresa pro nastavení nového hesla'),
		];
	}

	/**
	 * @Route("/mail/template/")
	 */
	public function templateAction(Request $request) {
		$allMailTemplatesData = $this->mailTemplateFacade->getAllMailTemplatesDataByDomainId($this->selectedDomain->getId());

		$form = $this->createForm($this->allMailTemplatesFormTypeFactory->create());

		$form->setData($allMailTemplatesData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->mailTemplateFacade->saveMailTemplatesData($allMailTemplatesData->getAllTemplates(), $allMailTemplatesData->domainId);

			$this->getFlashMessageSender()->addSuccessFlash('Nastavení šablony e-mailu bylo upraveno');

			return $this->redirect($this->generateUrl('admin_mail_template'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$orderStatusesTemplateVariables = $this->orderMailService->getTemplateVariables();
		$registrationTemplateVariables = $this->customerMailService->getTemplateVariables();
		$resetPasswordTemplateVariables = array_unique(array_merge(
			$this->resetPasswordMail->getBodyVariables(),
			$this->resetPasswordMail->getSubjectVariables()
		));
		$resetPasswordTemplateRequiredVariables = array_unique(array_merge(
			$this->resetPasswordMail->getRequiredBodyVariables(),
			$this->resetPasswordMail->getRequiredSubjectVariables()
		));

		return $this->render('@SS6Shop/Admin/Content/Mail/template.html.twig', [
			'form' => $form->createView(),
			'orderStatusesIndexedById' => $this->mailTemplateFacade->getAllIndexedById(),
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

			$this->getFlashMessageSender()->addSuccessFlash('Nastavení emailů bylo upraveno.');
		}

		return $this->render('@SS6Shop/Admin/Content/Mail/setting.html.twig', [
			'form' => $form->createView(),
		]);
	}

}

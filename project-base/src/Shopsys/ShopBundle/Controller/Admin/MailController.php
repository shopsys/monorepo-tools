<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Form\Admin\Mail\AllMailTemplatesFormTypeFactory;
use Shopsys\ShopBundle\Form\Admin\Mail\MailSettingFormType;
use Shopsys\ShopBundle\Model\Customer\Mail\RegistrationMailService;
use Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail;
use Shopsys\ShopBundle\Model\Mail\MailTemplate;
use Shopsys\ShopBundle\Model\Mail\MailTemplateFacade;
use Shopsys\ShopBundle\Model\Mail\Setting\MailSettingFacade;
use Shopsys\ShopBundle\Model\Order\Mail\OrderMailService;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatus;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusFacade;
use Symfony\Component\HttpFoundation\Request;

class MailController extends AdminBaseController {

    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Mail\AllMailTemplatesFormTypeFactory
     */
    private $allMailTemplatesFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\Mail\RegistrationMailService
     */
    private $registrationMailService;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail
     */
    private $resetPasswordMail;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailTemplateFacade
     */
    private $mailTemplateFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\Setting\MailSettingFacade
     */
    private $mailSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Mail\OrderMailService
     */
    private $orderMailService;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatusFacade
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
            OrderMailService::VARIABLE_NUMBER => t('Order number'),
            OrderMailService::VARIABLE_DATE => t('Date and time of order creation'),
            OrderMailService::VARIABLE_URL => t('E-shop URL address'),
            OrderMailService::VARIABLE_TRANSPORT => t('Chosen shipping name'),
            OrderMailService::VARIABLE_PAYMENT => t('Chosen payment name'),
            OrderMailService::VARIABLE_TOTAL_PRICE => t('Total order price (including VAT)'),
            OrderMailService::VARIABLE_BILLING_ADDRESS => t(
                'Billing address - name, surname, company, company number, tax number and billing address'
            ),
            OrderMailService::VARIABLE_DELIVERY_ADDRESS => t('Delivery address'),
            OrderMailService::VARIABLE_NOTE => t('Note'),
            OrderMailService::VARIABLE_PRODUCTS => t(
                'List of products in order (name, quantity, price per unit including VAT, total price per item including VAT)'
            ),
            OrderMailService::VARIABLE_ORDER_DETAIL_URL => t('Order detail URL address'),
            OrderMailService::VARIABLE_TRANSPORT_INSTRUCTIONS => t('Shipping instructions'),
            OrderMailService::VARIABLE_PAYMENT_INSTRUCTIONS => t('Payment instructions'),
        ];
    }

    /**
     * @return array
     */
    private function getRegistrationVariablesLabels() {
        return [
            RegistrationMailService::VARIABLE_FIRST_NAME => t('First name'),
            RegistrationMailService::VARIABLE_LAST_NAME => t('Surname'),
            RegistrationMailService::VARIABLE_EMAIL => t('E-mail'),
            RegistrationMailService::VARIABLE_URL => t('E-shop URL address'),
            RegistrationMailService::VARIABLE_LOGIN_PAGE => t('Link to the log in page'),
        ];
    }

    /**
     * @return array
     */
    private function getResetPasswordVariablesLabels() {
        return [
            ResetPasswordMail::VARIABLE_EMAIL => t('E-mail'),
            ResetPasswordMail::VARIABLE_NEW_PASSWORD_URL => t('New password settings URL address'),
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

            $this->getFlashMessageSender()->addSuccessFlash(t('E-mail templates settings modified'));

            return $this->redirectToRoute('admin_mail_template');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
        }

        $templateParameters = $this->getTemplateParameters();
        $templateParameters['form'] = $form->createView();

        return $this->render('@ShopsysShop/Admin/Content/Mail/template.html.twig', $templateParameters);
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

            $this->getFlashMessageSender()->addSuccessFlash(t('E-mail settings modified.'));
        }

        return $this->render('@ShopsysShop/Admin/Content/Mail/setting.html.twig', [
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

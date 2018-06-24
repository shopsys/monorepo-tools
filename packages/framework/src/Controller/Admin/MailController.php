<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Form\Admin\Mail\AllMailTemplatesFormType;
use Shopsys\FrameworkBundle\Form\Admin\Mail\MailSettingFormType;
use Shopsys\FrameworkBundle\Model\Customer\Mail\RegistrationMailService;
use Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMail;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade;
use Shopsys\FrameworkBundle\Model\Mail\Setting\MailSettingFacade;
use Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailService;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade;
use Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMail;
use Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataExportMail;
use Symfony\Component\HttpFoundation\Request;

class MailController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\Mail\RegistrationMailService
     */
    private $registrationMailService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMail
     */
    private $resetPasswordMail;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade
     */
    private $mailTemplateFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\Setting\MailSettingFacade
     */
    private $mailSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailService
     */
    private $orderMailService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade
     */
    private $orderStatusFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMail
     */
    private $personalDataAccessMail;

    /**
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataExportMail
     */
    private $personalDataExportMail;

    public function __construct(
        ResetPasswordMail $resetPasswordMail,
        OrderMailService $orderMailService,
        RegistrationMailService $registrationMailService,
        AdminDomainTabsFacade $adminDomainTabsFacade,
        MailTemplateFacade $mailTemplateFacade,
        MailSettingFacade $mailSettingFacade,
        OrderStatusFacade $orderStatusFacade,
        PersonalDataAccessMail $personalDataAccessMail,
        PersonalDataExportMail $personalDataExportMail
    ) {
        $this->resetPasswordMail = $resetPasswordMail;
        $this->orderMailService = $orderMailService;
        $this->registrationMailService = $registrationMailService;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->mailTemplateFacade = $mailTemplateFacade;
        $this->mailSettingFacade = $mailSettingFacade;
        $this->orderStatusFacade = $orderStatusFacade;
        $this->personalDataAccessMail = $personalDataAccessMail;
        $this->personalDataExportMail = $personalDataExportMail;
    }

    /**
     * @return array
     */
    private function getOrderStatusVariablesLabels()
    {
        return [
            OrderMailService::VARIABLE_NUMBER => t('Order number'),
            OrderMailService::VARIABLE_DATE => t('Date and time of order creation'),
            OrderMailService::VARIABLE_URL => t('E-shop URL address'),
            OrderMailService::VARIABLE_TRANSPORT => t('Chosen shipping name'),
            OrderMailService::VARIABLE_PAYMENT => t('Chosen payment name'),
            OrderMailService::VARIABLE_TOTAL_PRICE => t('Total order price (including VAT)'),
            OrderMailService::VARIABLE_BILLING_ADDRESS => t(
                'Billing address - name, last name, company, company number, tax number and billing address'
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
    private function getRegistrationVariablesLabels()
    {
        return [
            RegistrationMailService::VARIABLE_FIRST_NAME => t('First name'),
            RegistrationMailService::VARIABLE_LAST_NAME => t('Last name'),
            RegistrationMailService::VARIABLE_EMAIL => t('E-mail'),
            RegistrationMailService::VARIABLE_URL => t('E-shop URL address'),
            RegistrationMailService::VARIABLE_LOGIN_PAGE => t('Link to the log in page'),
        ];
    }

    /**
     * @return array
     */
    private function getResetPasswordVariablesLabels()
    {
        return [
            ResetPasswordMail::VARIABLE_EMAIL => t('E-mail'),
            ResetPasswordMail::VARIABLE_NEW_PASSWORD_URL => t('New password settings URL address'),
        ];
    }

    /**
     * @return array
     */
    private function getPersonalDataAccessVariablesLabels()
    {
        return [
            PersonalDataAccessMail::VARIABLE_DOMAIN => t('E-shop name'),
            PersonalDataAccessMail::VARIABLE_EMAIL => t('E-mail'),
            PersonalDataAccessMail::VARIABLE_URL => t('E-shop URL address'),
        ];
    }

    /**
     * @return array
     */
    private function getPersonalExportVariablesLabels()
    {
        return [
            PersonalDataExportMail::VARIABLE_DOMAIN => t('E-shop name'),
            PersonalDataExportMail::VARIABLE_EMAIL => t('E-mail'),
            PersonalDataExportMail::VARIABLE_URL => t('E-shop URL address'),
        ];
    }

    /**
     * @return array
     */
    private function getTemplateParameters()
    {
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

        $selectedDomainId = $this->adminDomainTabsFacade->getSelectedDomainId();
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
        $personalDataAccessTemplate = $this->mailTemplateFacade->get(
            MailTemplate::PERSONAL_DATA_ACCESS_NAME,
            $selectedDomainId
        );

        $personalDataExportTemplate = $this->mailTemplateFacade->get(
            MailTemplate::PERSONAL_DATA_EXPORT_NAME,
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
            'personalDataAccessTemplate' => $personalDataAccessTemplate,
            'personalDataAccessVariables' => $this->personalDataAccessMail->getSubjectVariables(),
            'personalDataAccessRequiredVariablesLabels' => $this->personalDataAccessMail->getRequiredBodyVariables(),
            'personalDataAccessVariablesLabels' => $this->getPersonalDataAccessVariablesLabels(),
            'personalDataExportTemplate' => $personalDataExportTemplate,
            'personalDataExportVariables' => $this->personalDataExportMail->getSubjectVariables(),
            'personalDataExportRequiredVariablesLabels' => $this->personalDataExportMail->getRequiredBodyVariables(),
            'personalDataExportVariablesLabels' => $this->getPersonalExportVariablesLabels(),
        ];
    }

    /**
     * @Route("/mail/template/")
     */
    public function templateAction(Request $request)
    {
        $allMailTemplatesData = $this->mailTemplateFacade->getAllMailTemplatesDataByDomainId(
            $this->adminDomainTabsFacade->getSelectedDomainId()
        );

        $form = $this->createForm(AllMailTemplatesFormType::class, $allMailTemplatesData);
        $form->handleRequest($request);
        $allMailTemplatesData->getAllTemplates();

        if ($form->isSubmitted() && $form->isValid()) {
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

        return $this->render('@ShopsysFramework/Admin/Content/Mail/template.html.twig', $templateParameters);
    }

    /**
     * @Route("/mail/setting/")
     */
    public function settingAction(Request $request)
    {
        $selectedDomainId = $this->adminDomainTabsFacade->getSelectedDomainId();

        $mailSettingData = [
            'email' => $this->mailSettingFacade->getMainAdminMail($selectedDomainId),
            'name' => $this->mailSettingFacade->getMainAdminMailName($selectedDomainId),
        ];

        $form = $this->createForm(MailSettingFormType::class, $mailSettingData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailSettingData = $form->getData();

            $this->mailSettingFacade->setMainAdminMail($mailSettingData['email'], $selectedDomainId);
            $this->mailSettingFacade->setMainAdminMailName($mailSettingData['name'], $selectedDomainId);

            $this->getFlashMessageSender()->addSuccessFlash(t('E-mail settings modified.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Mail/setting.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

<?php

namespace Shopsys\ShopBundle\Model\Mail;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFileFacade;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusMailTemplateService;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusRepository;

class MailTemplateFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailTemplateRepository
     */
    private $mailTemplateRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatusRepository
     */
    private $orderStatusRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatusMailTemplateService
     */
    private $orderStatusMailTemplateService;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain;
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\UploadedFile\UploadedFileFacade
     */
    private $uploadedFileFacade;

    public function __construct(
        EntityManager $em,
        MailTemplateRepository $mailTemplateRepository,
        OrderStatusRepository $orderStatusRepository,
        OrderStatusMailTemplateService $orderStatusMailTemplateService,
        Domain $domain,
        UploadedFileFacade $uploadedFileFacade
    ) {
        $this->em = $em;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderStatusMailTemplateService = $orderStatusMailTemplateService;
        $this->domain = $domain;
        $this->uploadedFileFacade = $uploadedFileFacade;
    }

    /**
     * @param string $templateName
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Mail\MailTemplate
     */
    public function get($templateName, $domainId)
    {
        return $this->mailTemplateRepository->getByNameAndDomainId($templateName, $domainId);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Mail\MailTemplate[]
     */
    public function getOrderStatusMailTemplatesIndexedByOrderStatusId($domainId)
    {
        $orderStatuses = $this->orderStatusRepository->getAll();
        $mailTemplates = $this->mailTemplateRepository->getAllByDomainId($domainId);

        return $this->orderStatusMailTemplateService->getFilteredOrderStatusMailTemplatesIndexedByOrderStatusId(
            $orderStatuses,
            $mailTemplates
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplateData[] $mailTemplatesData
     * @param int $domainId
     */
    public function saveMailTemplatesData(array $mailTemplatesData, $domainId)
    {
        foreach ($mailTemplatesData as $mailTemplateData) {
            $mailTemplate = $this->mailTemplateRepository->getByNameAndDomainId($mailTemplateData->name, $domainId);
            $mailTemplate->edit($mailTemplateData);
            if ($mailTemplateData->deleteAttachment === true) {
                $this->uploadedFileFacade->deleteUploadedFileByEntity($mailTemplate);
            }
            $this->uploadedFileFacade->uploadFile($mailTemplate, $mailTemplateData->attachment);
        }

        $this->em->flush();
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Mail\AllMailTemplatesData
     */
    public function getAllMailTemplatesDataByDomainId($domainId)
    {
        $orderStatuses = $this->orderStatusRepository->getAll();
        $mailTemplates = $this->mailTemplateRepository->getAllByDomainId($domainId);

        $allMailTemplatesData = new AllMailTemplatesData();
        $allMailTemplatesData->domainId = $domainId;

        $registrationMailTemplatesData = new MailTemplateData();
        $registrationMailTemplate = $this->mailTemplateRepository
            ->findByNameAndDomainId(MailTemplate::REGISTRATION_CONFIRM_NAME, $domainId);
        if ($registrationMailTemplate !== null) {
            $registrationMailTemplatesData->setFromEntity($registrationMailTemplate);
        }
        $registrationMailTemplatesData->name = MailTemplate::REGISTRATION_CONFIRM_NAME;
        $allMailTemplatesData->registrationTemplate = $registrationMailTemplatesData;

        $resetPasswordMailTemplateData = new MailTemplateData();
        $resetPasswordMailTemplate = $this->mailTemplateRepository
            ->findByNameAndDomainId(MailTemplate::RESET_PASSWORD_NAME, $domainId);
        if ($resetPasswordMailTemplate !== null) {
            $resetPasswordMailTemplateData->setFromEntity($resetPasswordMailTemplate);
        }
        $resetPasswordMailTemplateData->name = MailTemplate::RESET_PASSWORD_NAME;
        $allMailTemplatesData->resetPasswordTemplate = $resetPasswordMailTemplateData;

        $allMailTemplatesData->orderStatusTemplates =
            $this->orderStatusMailTemplateService->getOrderStatusMailTemplatesData($orderStatuses, $mailTemplates);

        return $allMailTemplatesData;
    }

    /**
     * @param string $name
     */
    public function createMailTemplateForAllDomains($name)
    {
        foreach ($this->domain->getAll() as $domainConfig) {
            $mailTemplate = new MailTemplate($name, $domainConfig->getId(), new MailTemplateData());
            $this->em->persist($mailTemplate);
        }

        $this->em->flush();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplate $mailTemplate
     * @return string[]
     */
    public function getMailTemplateAttachmentsFilepaths(MailTemplate $mailTemplate)
    {
        $filepaths = [];
        if ($this->uploadedFileFacade->hasUploadedFile($mailTemplate)) {
            $uploadedFile = $this->uploadedFileFacade->getUploadedFileByEntity($mailTemplate);
            $filepaths[] = $this->uploadedFileFacade->getAbsoluteUploadedFileFilepath($uploadedFile);
        }

        return $filepaths;
    }

    /**
     * @return bool
     */
    public function existsTemplateWithEnabledSendingHavingEmptyBodyOrSubject()
    {
        return $this->mailTemplateRepository->existsTemplateWithEnabledSendingHavingEmptyBodyOrSubject();
    }
}

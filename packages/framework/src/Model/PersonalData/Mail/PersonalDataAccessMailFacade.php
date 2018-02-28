<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData\Mail;

use Shopsys\FrameworkBundle\Model\Mail\MailerService;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequest;

class PersonalDataAccessMailFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailerService
     */
    private $mailer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade
     */
    private $mailTemplateFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMail
     */
    private $personalDataAccessMail;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailerService $mailer
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMail $personalDataAccessMail
     */
    public function __construct(
        MailerService $mailer,
        MailTemplateFacade $mailTemplateFacade,
        PersonalDataAccessMail $personalDataAccessMail
    ) {
        $this->mailer = $mailer;
        $this->mailTemplateFacade = $mailTemplateFacade;
        $this->personalDataAccessMail = $personalDataAccessMail;
    }

    /**
     * @param PersonalDataAccessRequest $personalDataAccessRequest
     */
    public function sendMail(PersonalDataAccessRequest $personalDataAccessRequest)
    {
        $mailTemplate = $this->mailTemplateFacade->get(
            MailTemplate::PERSONAL_DATA_ACCESS_NAME,
            $personalDataAccessRequest->getDomainId()
        );

        $messageData = $this->personalDataAccessMail->createMessage($mailTemplate, $personalDataAccessRequest);
        $messageData->attachmentsFilepaths = $this->mailTemplateFacade->getMailTemplateAttachmentsFilepaths($mailTemplate);

        $this->mailer->send($messageData);
    }
}

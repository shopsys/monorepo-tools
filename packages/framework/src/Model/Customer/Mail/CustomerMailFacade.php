<?php

namespace Shopsys\FrameworkBundle\Model\Customer\Mail;

use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Mail\MailerService;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade;

class CustomerMailFacade
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
     * @var \Shopsys\FrameworkBundle\Model\Customer\Mail\RegistrationMailService
     */
    private $registrationMailService;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailerService $mailer
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
     * @param \Shopsys\FrameworkBundle\Model\Customer\Mail\RegistrationMailService $registrationMailService
     */
    public function __construct(
        MailerService $mailer,
        MailTemplateFacade $mailTemplateFacade,
        RegistrationMailService $registrationMailService
    ) {
        $this->mailer = $mailer;
        $this->mailTemplateFacade = $mailTemplateFacade;
        $this->registrationMailService = $registrationMailService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     */
    public function sendRegistrationMail(User $user)
    {
        $mailTemplate = $this->mailTemplateFacade->get(MailTemplate::REGISTRATION_CONFIRM_NAME, $user->getDomainId());
        $messageData = $this->registrationMailService->getMessageDataByUser($user, $mailTemplate);
        $messageData->attachmentsFilepaths = $this->mailTemplateFacade->getMailTemplateAttachmentsFilepaths($mailTemplate);
        $this->mailer->send($messageData);
    }
}

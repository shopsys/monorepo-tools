<?php

namespace Shopsys\FrameworkBundle\Model\Customer\Mail;

use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Mail\MailerService;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade;

class ResetPasswordMailFacade
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
     * @var \Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMail
     */
    private $resetPasswordMail;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailerService $mailer
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
     * @param \Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMail $resetPasswordMail
     */
    public function __construct(
        MailerService $mailer,
        MailTemplateFacade $mailTemplateFacade,
        ResetPasswordMail $resetPasswordMail
    ) {
        $this->mailer = $mailer;
        $this->mailTemplateFacade = $mailTemplateFacade;
        $this->resetPasswordMail = $resetPasswordMail;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     */
    public function sendMail(User $user)
    {
        $mailTemplate = $this->mailTemplateFacade->get(MailTemplate::RESET_PASSWORD_NAME, $user->getDomainId());
        $messageData = $this->resetPasswordMail->createMessage($mailTemplate, $user);
        $messageData->attachmentsFilepaths = $this->mailTemplateFacade->getMailTemplateAttachmentsFilepaths($mailTemplate);
        $this->mailer->send($messageData);
    }
}

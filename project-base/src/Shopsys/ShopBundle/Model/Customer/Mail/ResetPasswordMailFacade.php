<?php

namespace Shopsys\ShopBundle\Model\Customer\Mail;

use Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Mail\MailerService;
use Shopsys\ShopBundle\Model\Mail\MailTemplate;
use Shopsys\ShopBundle\Model\Mail\MailTemplateFacade;

class ResetPasswordMailFacade
{

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailerService
     */
    private $mailer;

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailTemplateFacade
     */
    private $mailTemplateFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail
     */
    private $resetPasswordMail;

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\MailerService $mailer
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
     * @param \Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail $resetPasswordMail
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
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     */
    public function sendMail(User $user) {
        $mailTemplate = $this->mailTemplateFacade->get(MailTemplate::RESET_PASSWORD_NAME, $user->getDomainId());
        $messageData = $this->resetPasswordMail->createMessage($mailTemplate, $user);
        $messageData->attachmentsFilepaths = $this->mailTemplateFacade->getMailTemplateAttachmentsFilepaths($mailTemplate);
        $this->mailer->send($messageData);
    }
}

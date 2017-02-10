<?php

namespace Shopsys\ShopBundle\Model\Customer\Mail;

use Shopsys\ShopBundle\Model\Customer\Mail\RegistrationMailService;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Mail\MailerService;
use Shopsys\ShopBundle\Model\Mail\MailTemplate;
use Shopsys\ShopBundle\Model\Mail\MailTemplateFacade;

class CustomerMailFacade
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
     * @var \Shopsys\ShopBundle\Model\Customer\Mail\RegistrationMailService
     */
    private $registrationMailService;

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\MailerService $mailer
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
     * @param \Shopsys\ShopBundle\Model\Customer\Mail\RegistrationMailService $registrationMailService
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
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     */
    public function sendRegistrationMail(User $user) {
        $mailTemplate = $this->mailTemplateFacade->get(MailTemplate::REGISTRATION_CONFIRM_NAME, $user->getDomainId());
        $messageData = $this->registrationMailService->getMessageDataByUser($user, $mailTemplate);
        $messageData->attachmentsFilepaths = $this->mailTemplateFacade->getMailTemplateAttachmentsFilepaths($mailTemplate);
        $this->mailer->send($messageData);
    }
}

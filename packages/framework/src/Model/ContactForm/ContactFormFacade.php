<?php

namespace Shopsys\FrameworkBundle\Model\ContactForm;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Mail\Mailer;
use Shopsys\FrameworkBundle\Model\Mail\MessageData;
use Shopsys\FrameworkBundle\Model\Mail\Setting\MailSettingFacade;
use Twig_Environment;

class ContactFormFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\Setting\MailSettingFacade
     */
    protected $mailSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\Mailer
     */
    protected $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\Setting\MailSettingFacade $mailSettingFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Mail\Mailer $mailer
     * @param \Twig_Environment $twig
     */
    public function __construct(
        MailSettingFacade $mailSettingFacade,
        Domain $domain,
        Mailer $mailer,
        Twig_Environment $twig
    ) {
        $this->mailSettingFacade = $mailSettingFacade;
        $this->domain = $domain;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\ContactForm\ContactFormData $contactFormData
     */
    public function sendMail(ContactFormData $contactFormData)
    {
        $messageData = new MessageData(
            $this->mailSettingFacade->getMainAdminMail($this->domain->getId()),
            null,
            $this->getMailBody($contactFormData),
            t('Contact form'),
            $contactFormData->email,
            $contactFormData->name
        );
        $this->mailer->send($messageData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\ContactForm\ContactFormData $contactFormData
     * @return string
     */
    protected function getMailBody($contactFormData)
    {
        return $this->twig->render('@ShopsysFramework/Mail/ContactForm/mail.html.twig', [
            'contactFormData' => $contactFormData,
        ]);
    }
}

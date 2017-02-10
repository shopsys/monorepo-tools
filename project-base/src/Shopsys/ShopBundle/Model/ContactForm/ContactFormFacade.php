<?php

namespace Shopsys\ShopBundle\Model\ContactForm;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\ContactForm\ContactFormData;
use Shopsys\ShopBundle\Model\Mail\MailerService;
use Shopsys\ShopBundle\Model\Mail\MessageData;
use Shopsys\ShopBundle\Model\Mail\Setting\MailSettingFacade;
use Twig_Environment;

class ContactFormFacade {

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\Setting\MailSettingFacade
     */
    private $mailSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailerService
     */
    private $mailerService;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\Setting\MailSettingFacade $mailSettingFacade
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     * @param \Shopsys\ShopBundle\Model\Mail\MailerService $mailerService
     * @param \Twig_Environment $twig
     */
    public function __construct(
        MailSettingFacade $mailSettingFacade,
        Domain $domain,
        MailerService $mailerService,
        Twig_Environment $twig
    ) {
        $this->mailSettingFacade = $mailSettingFacade;
        $this->domain = $domain;
        $this->mailerService = $mailerService;
        $this->twig = $twig;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\ContactForm\ContactFormData $contactFormData
     */
    public function sendMail(ContactFormData $contactFormData) {
        $messageData = new MessageData(
            $this->mailSettingFacade->getMainAdminMail($this->domain->getId()),
            null,
            $this->getMailBody($contactFormData),
            t('Contact form'),
            $contactFormData->email,
            $contactFormData->name
        );
        $this->mailerService->send($messageData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\ContactForm\ContactFormData $contactFormData
     * @return string
     */
    private function getMailBody($contactFormData) {
        return $this->twig->render('@ShopsysShop/Mail/ContactForm/mail.html.twig', [
            'contactFormData' => $contactFormData,
        ]);
    }
}

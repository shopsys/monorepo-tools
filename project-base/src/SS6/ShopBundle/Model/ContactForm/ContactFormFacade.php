<?php

namespace SS6\ShopBundle\Model\ContactForm;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\ContactForm\ContactFormData;
use SS6\ShopBundle\Model\Mail\MailerService;
use SS6\ShopBundle\Model\Mail\MessageData;
use SS6\ShopBundle\Model\Mail\Setting\MailSettingFacade;
use Twig_Environment;

class ContactFormFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Mail\Setting\MailSettingFacade
	 */
	private $mailSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailerService
	 */
	private $mailerService;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @param \SS6\ShopBundle\Model\Mail\Setting\MailSettingFacade $mailSettingFacade
	 * @param \SS6\ShopBundle\Component\Domain\Domain $domain
	 * @param \SS6\ShopBundle\Model\Mail\MailerService $mailerService
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
	 * @param \SS6\ShopBundle\Model\ContactForm\ContactFormData $contactFormData
	 */
	public function sendMail(ContactFormData $contactFormData) {
		$messageData = new MessageData(
			$this->mailSettingFacade->getMainAdminMail($this->domain->getId()),
			null,
			$this->getMailBody($contactFormData),
			t('Kontaktní formulář'),
			$contactFormData->email,
			$contactFormData->name
		);
		$this->mailerService->send($messageData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\ContactForm\ContactFormData $contactFormData
	 * @return string
	 */
	private function getMailBody($contactFormData) {
		return $this->twig->render('@SS6Shop/Mail/ContactForm/mail.html.twig', [
			'contactFormData' => $contactFormData,
		]);
	}
}

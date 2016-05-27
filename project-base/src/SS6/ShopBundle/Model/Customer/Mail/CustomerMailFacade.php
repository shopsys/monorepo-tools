<?php

namespace SS6\ShopBundle\Model\Customer\Mail;

use SS6\ShopBundle\Model\Customer\Mail\RegistrationMailService;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Mail\MailerService;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MailTemplateFacade;

class CustomerMailFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailerService
	 */
	private $mailer;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateFacade
	 */
	private $mailTemplateFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\Mail\RegistrationMailService
	 */
	private $registrationMailService;

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailerService $mailer
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
	 * @param \SS6\ShopBundle\Model\Customer\Mail\RegistrationMailService $registrationMailService
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
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function sendRegistrationMail(User $user) {
		$mailTemplate = $this->mailTemplateFacade->get(MailTemplate::REGISTRATION_CONFIRM_NAME, $user->getDomainId());
		$messageData = $this->registrationMailService->getMessageDataByUser($user, $mailTemplate);
		$messageData->attachmentsFilepaths = $this->mailTemplateFacade->getMailTemplateAttachmentsFilepaths($mailTemplate);
		$this->mailer->send($messageData);
	}
}

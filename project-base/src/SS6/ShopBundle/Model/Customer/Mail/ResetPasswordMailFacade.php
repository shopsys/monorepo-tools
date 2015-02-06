<?php

namespace SS6\ShopBundle\Model\Customer\Mail;

use SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMailService;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Mail\MailerService;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MailTemplateFacade;

class ResetPasswordMailFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailerService
	 */
	private $mailer;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateFacade
	 */
	private $mailTemplateFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMailService
	 */
	private $resetPasswordMailService;

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailerService $mailer
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
	 * @param \SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMailService $resetPasswordMailService
	 */
	public function __construct(
		MailerService $mailer,
		MailTemplateFacade $mailTemplateFacade,
		ResetPasswordMailService $resetPasswordMailService
	) {
		$this->mailer = $mailer;
		$this->mailTemplateFacade = $mailTemplateFacade;
		$this->resetPasswordMailService = $resetPasswordMailService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function sendMail(User $user) {
		$mailTemplate = $this->mailTemplateFacade->get(MailTemplate::RESET_PASSWORD_NAME, $user->getDomainId());
		$messageData = $this->resetPasswordMailService->getMessageData($user, $mailTemplate);
		$this->mailer->send($messageData);
	}

}

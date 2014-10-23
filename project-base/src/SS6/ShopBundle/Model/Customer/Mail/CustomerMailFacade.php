<?php

namespace SS6\ShopBundle\Model\Customer\Mail;

use SS6\ShopBundle\Model\Customer\Mail\CustomerMailService;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Mail\MailTemplateFacade;
use Swift_Mailer;

class CustomerMailFacade {

	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateFacade
	 */
	private $mailTemplateFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\Mail\CustomerMailService
	 */
	private $customerMailService;

	/**
	 * @param \Swift_Mailer $mailer
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
	 * @param \SS6\ShopBundle\Model\Customer\Mail\CustomerMailService $customerMailService
	 */
	public function __construct(
		Swift_Mailer $mailer,
		MailTemplateFacade $mailTemplateFacade,
		CustomerMailService $customerMailService
	) {
		$this->mailer = $mailer;
		$this->mailTemplateFacade = $mailTemplateFacade;
		$this->customerMailService = $customerMailService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @throws \SS6\ShopBundle\Model\Customer\Mail\Exception\SendMailFailedException
	 */
	public function sendRegistrationMail(User $user) {
		$mailTemplate = $this->mailTemplateFacade->get('registration_confirm');
		$message = $this->customerMailService->getMessageByUser($user, $mailTemplate);

		$failedRecipients = array();
		$successSend = $this->mailer->send($message, $failedRecipients);
		if (!$successSend && count($failedRecipients) > 0) {
			throw new \SS6\ShopBundle\Model\Customer\Mail\Exception\SendMailFailedException($failedRecipients);
		}
	}
}

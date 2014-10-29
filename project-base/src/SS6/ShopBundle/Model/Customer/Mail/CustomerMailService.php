<?php

namespace SS6\ShopBundle\Model\Customer\Mail;

use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use Swift_Message;

class CustomerMailService {

	const VARIABLE_FIRST_NAME = '{first_name}';
	const VARIABLE_LAST_NAME = '{last_name}';
	const VARIABLE_EMAIL = '{email}';
	const VARIABLE_URL = '{url}';
	const VARIABLE_LOGIN_PAGE = '{login_page}';

	/**
	 * @var string
	 */
	private $senderEmail;

	/**
	 * @param string $senderEmail
	 */
	public function __construct($senderEmail) {
		$this->senderEmail = $senderEmail;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $mailTemplate
	 * @return \Swift_Message
	 */
	public function getMessageByUser(User $user, MailTemplate $mailTemplate) {
		$toEmail = $user->getEmail();
		$body = $mailTemplate->getBody();
		$subject = $mailTemplate->getSubject();

		$message = Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom($this->senderEmail)
			->setTo($toEmail)
			->setContentType('text/plain; charset=UTF-8')
			->setBody(strip_tags($body), 'text/plain')
			->addPart($body, 'text/html');

		return $message;
	}

	/**
	 * @return array
	 */
	public function getRegistrationTemplateVariables() {
		return array(
			self::VARIABLE_FIRST_NAME => 'Jméno',
			self::VARIABLE_LAST_NAME => 'Příjmení',
			self::VARIABLE_EMAIL => 'Email',
			self::VARIABLE_URL => 'URL adresa e-shopu',
			self::VARIABLE_LOGIN_PAGE => 'Odkaz na stránku s přihlášením',
		);
	}
}

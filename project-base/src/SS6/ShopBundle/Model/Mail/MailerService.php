<?php

namespace SS6\ShopBundle\Model\Mail;

use Swift_Mailer;
use Swift_Message;

class MailerService {

	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

	/**
	 * @param Swift_Mailer $mailer
	 */
	public function __construct(Swift_Mailer $mailer) {
		$this->mailer = $mailer;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MessageData $messageData
	 * @throws \SS6\ShopBundle\Model\Mail\Exception\SendMailFailedException
	 */
	public function send(MessageData $messageData) {
		$message = $this->getMessageWithReplacedVariables($messageData);
		$failedRecipients = array();
		$successSend = $this->mailer->send($message, $failedRecipients);
		if (!$successSend && count($failedRecipients) > 0) {
			throw new \SS6\ShopBundle\Model\Mail\Exception\SendMailFailedException($failedRecipients);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MessageData $messageData
	 * @return \Swift_Message
	 */
	private function getMessageWithReplacedVariables(MessageData $messageData) {
		$toEmail = $messageData->getToEmail();
		$body = $this->replaceVariables(
			$messageData->getBody(),
			$messageData->getVariablesReplacementsForBody());
		$subject = $this->replaceVariables(
			$messageData->getSubject(),
			$messageData->getVariablesReplacementsForSubject());
		$fromEmail = $messageData->getFromEmail();
		$fromName = $messageData->getFromName();

		$message = Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom(
				$fromEmail,
				$fromName
			)
			->setTo($toEmail)
			->setContentType('text/plain; charset=UTF-8')
			->setBody(strip_tags($body), 'text/plain')
			->addPart($body, 'text/html');

		return $message;
	}

	/**
	 * @param string $string
	 * @param array $variablesKeysAndValues
	 * @return string
	 */
	private function replaceVariables($string, $variablesKeysAndValues) {
		return strtr($string, $variablesKeysAndValues);
	}
}

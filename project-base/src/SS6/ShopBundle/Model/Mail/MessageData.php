<?php

namespace SS6\ShopBundle\Model\Mail;

class MessageData {

	/**
	 * @var string
	 */
	public $toEmail;

	/**
	 * @var string
	 */
	public $body;

	/**
	 * @var string
	 */
	public $subject;

	/**
	 * @var string
	 */
	public $fromEmail;

	/**
	 * @var string
	 */
	public $fromName;

	/**
	 * @var array
	 */
	public $variablesReplacementsForSubject;

	/**
	 * @var array
	 */
	public $variablesReplacementsForBody;

	/**
	 * @param string $toEmail
	 * @param string $body
	 * @param string $subject
	 * @param string $fromEmail
	 * @param string $fromName
	 * @param array $variablesReplacementsForBody
	 * @param array|null $variablesReplacementsForSubject
	 */
	public function __construct(
		$toEmail,
		$body,
		$subject,
		$fromEmail,
		$fromName,
		$variablesReplacementsForBody,
		$variablesReplacementsForSubject = null
	) {
		$this->toEmail = $toEmail;
		$this->body = $body;
		$this->subject = $subject;
		$this->fromEmail = $fromEmail;
		$this->fromName = $fromName;
		$this->variablesReplacementsForBody = $variablesReplacementsForBody;
		if ($variablesReplacementsForSubject !== null) {
			$this->variablesReplacementsForSubject = $variablesReplacementsForSubject;
		} else {
			$this->variablesReplacementsForSubject = $variablesReplacementsForBody;
		}
	}

}

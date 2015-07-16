<?php

namespace SS6\ShopBundle\Model\Mail;

class MessageData {

	/**
	 * @var string
	 */
	public $toEmail;

	/**
	 * @var string|null
	 */
	public $bccEmail;

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
	 * @param string|null $bccEmail
	 * @param string $body
	 * @param string $subject
	 * @param string $fromEmail
	 * @param string $fromName
	 * @param string[] $variablesReplacementsForBody
	 * @param string[] $variablesReplacementsForSubject
	 */
	public function __construct(
		$toEmail,
		$bccEmail,
		$body,
		$subject,
		$fromEmail,
		$fromName,
		array $variablesReplacementsForBody = [],
		array $variablesReplacementsForSubject = []
	) {
		$this->toEmail = $toEmail;
		$this->bccEmail = $bccEmail;
		$this->body = $body;
		$this->subject = $subject;
		$this->fromEmail = $fromEmail;
		$this->fromName = $fromName;
		$this->variablesReplacementsForBody = $variablesReplacementsForBody;
		if (!empty($variablesReplacementsForSubject)) {
			$this->variablesReplacementsForSubject = $variablesReplacementsForSubject;
		} else {
			$this->variablesReplacementsForSubject = $variablesReplacementsForBody;
		}
	}

}

<?php

namespace Shopsys\ShopBundle\Model\Mail;

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
	 * @var string
	 */
	public $variablesReplacementsForSubject;

	/**
	 * @var string[]
	 */
	public $variablesReplacementsForBody;

	/**
	 * @var string[]
	 */
	public $attachmentsFilepaths;

	/**
	 * @var string|null
	 */
	public $replyTo;

	/**
	 * @param string $toEmail
	 * @param string|null $bccEmail
	 * @param string $body
	 * @param string $subject
	 * @param string $fromEmail
	 * @param string $fromName
	 * @param string[] $variablesReplacementsForBody
	 * @param string[] $variablesReplacementsForSubject
	 * @param string[] $attachmentsFilepaths
	 * @param string|null $replyTo
	 */
	public function __construct(
		$toEmail,
		$bccEmail,
		$body,
		$subject,
		$fromEmail,
		$fromName,
		array $variablesReplacementsForBody = [],
		array $variablesReplacementsForSubject = [],
		array $attachmentsFilepaths = [],
		$replyTo = null
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
		$this->attachmentsFilepaths = $attachmentsFilepaths;
		$this->replyTo = $replyTo;
	}

}

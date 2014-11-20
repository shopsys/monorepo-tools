<?php

namespace SS6\ShopBundle\Model\Mail;

class MessageData {

	/**
	 * @var string
	 */
	private $toEmail;

	/**
	 * @var string
	 */
	private $body;

	/**
	 * @var string
	 */
	private $subject;

	/**
	 * @var string
	 */
	private $fromEmail;

	/**
	 * @var string
	 */
	private $fromName;

	/**
	 * @var array
	 */
	private $variablesReplacementsForSubject;

	/**
	 * @var array
	 */
	private $variablesReplacementsForBody;

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

	/**
	 * @return string
	 */
	public function getToEmail() {
		return $this->toEmail;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @return string
	 */
	public function getFromEmail() {
		return $this->fromEmail;
	}

	/**
	 * @return string
	 */
	public function getFromName() {
		return $this->fromName;
	}

	/**
	 * @return array
	 */
	public function getVariablesReplacementsForSubject() {
		return $this->variablesReplacementsForSubject;
	}

	/**
	 * @return array
	 */
	public function getVariablesReplacementsForBody() {
		return $this->variablesReplacementsForBody;
	}

}

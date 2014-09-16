<?php

namespace SS6\ShopBundle\Model\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplate;

class MailTemplateData {

	/**
	 * @var string|null
	 */
	private $subject;
	
	/**
	 * @var string|null
	 */
	private $body;

	/**
	 * @param string|null $subject
	 * @param string|null $body
	 */
	public function __construct($subject = null, $body = null) {
		$this->subject = $subject;
		$this->body = $body;
	}

	/**
	 * @return string|null
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @return string|null
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param string|null $subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * @param string|null $body
	 */
	public function setBody($body) {
		$this->body = $body;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $mailTemplate
	 */
	public function setFromEntity(MailTemplate $mailTemplate) {
		$this->subject = $mailTemplate->getSubject();
		$this->body = $mailTemplate->getBody();
	}
	
}

<?php

namespace SS6\ShopBundle\Model\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplate;

class MailTemplateData {

	/**
	 * @var string|null
	 */
	private $name;

	/**
	 * @var string|null
	 */
	private $subject;

	/**
	 * @var string|null
	 */
	private $body;

	/**
	 * @var bool
	 */
	private $sendMail;

	/**
	 * @param string|null $name
	 * @param string|null $subject
	 * @param string|null $body
	 * @param bool $sendMail
	 */
	public function __construct($name = null, $subject = null, $body = null, $sendMail = false) {
		$this->name = $name;
		$this->subject = $subject;
		$this->body = $body;
		$this->sendMail = $sendMail;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
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
	 * @return bool
	 */
	public function isSendMail() {
		return $this->sendMail;
	}

	/**
	 * @param string|null $name
	 */
	public function setName($name) {
		$this->name = $name;
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
	 * @param bool $sendMail
	 */
	public function setSendMail($sendMail) {
		$this->sendMail = $sendMail;
	}


	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $mailTemplate
	 */
	public function setFromEntity(MailTemplate $mailTemplate) {
		$this->name = $mailTemplate->getName();
		$this->subject = $mailTemplate->getSubject();
		$this->body = $mailTemplate->getBody();
		$this->sendMail = $mailTemplate->isSendMail();
	}

}

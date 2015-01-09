<?php

namespace SS6\ShopBundle\Model\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplate;

class MailTemplateData {

	/**
	 * @var string|null
	 */
	public $name;

	/**
	 * @var string|null
	 */
	public $subject;

	/**
	 * @var string|null
	 */
	public $body;

	/**
	 * @var bool
	 */
	public $sendMail;

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
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $mailTemplate
	 */
	public function setFromEntity(MailTemplate $mailTemplate) {
		$this->name = $mailTemplate->getName();
		$this->subject = $mailTemplate->getSubject();
		$this->body = $mailTemplate->getBody();
		$this->sendMail = $mailTemplate->isSendMail();
	}

}

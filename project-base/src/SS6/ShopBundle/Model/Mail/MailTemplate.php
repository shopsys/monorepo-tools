<?php

namespace SS6\ShopBundle\Model\Mail;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Mail\MailTemplateData;

/**
 * @ORM\Table(name="mail_templates")
 * @ORM\Entity
 */
class MailTemplate {

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 * @ORM\Id
	 */
	private $name;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $subject;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $body;

	/**
	 * @param string $name
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateData $mailTemplateData
	 */
	public function __construct($name, MailTemplateData $mailTemplateData = null) {
		$this->name = $name;
		$this->edit($mailTemplateData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateData $mailTemplateData
	 */
	public function edit(MailTemplateData $mailTemplateData = null) {
		if ($mailTemplateData !== null) {
			$this->subject = $mailTemplateData->getSubject();
			$this->body = $mailTemplateData->getBody();
		} else {
			$this->subject = '';
			$this->body = '';
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
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
	public function getBody() {
		return $this->body;
	}

}

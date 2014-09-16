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
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 * @ORM\Id
	 */
	private $subject;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $body;

	/**
	 * @param string $name
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateData $mailTemplateData
	 */
	public function __construct($name, MailTemplateData $mailTemplateData) {
		$this->name = $name;
		$this->edit($mailTemplateData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateData $mailTemplateData
	 */
	public function edit(MailTemplateData $mailTemplateData) {
		$this->subject = $mailTemplateData->getSubject();
		$this->body = $mailTemplateData->getBody();
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

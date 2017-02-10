<?php

namespace Shopsys\ShopBundle\Model\Mail;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Mail\MailTemplateData;

/**
 * @ORM\Table(
 *	name="mail_templates",
 *	uniqueConstraints={
 *		@ORM\UniqueConstraint(name="name_domain", columns={"name", "domain_id"})
 *	}
 * )
 * @ORM\Entity
 */
class MailTemplate {

	const REGISTRATION_CONFIRM_NAME = 'registration_confirm';
	const RESET_PASSWORD_NAME = 'reset_password';

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $bccEmail;

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
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $sendMail;

	/**
	 * @param string $name
	 * @param int $domainId
	 * @param \Shopsys\ShopBundle\Model\Mail\MailTemplateData $mailTemplateData
	 */
	public function __construct($name, $domainId, MailTemplateData $mailTemplateData) {
		$this->name = $name;
		$this->domainId = $domainId;
		$this->edit($mailTemplateData);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Mail\MailTemplateData $mailTemplateData
	 */
	public function edit(MailTemplateData $mailTemplateData) {
		$this->bccEmail = $mailTemplateData->bccEmail;
		$this->subject = $mailTemplateData->subject;
		$this->body = $mailTemplateData->body;
		$this->sendMail = $mailTemplateData->sendMail;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return string|null
	 */
	public function getBccEmail() {
		return $this->bccEmail;
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

	/**
	 * @return bool
	 */
	public function isSendMail() {
		return $this->sendMail;
	}
}

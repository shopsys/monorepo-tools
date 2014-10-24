<?php

namespace SS6\ShopBundle\Model\Mail;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Mail\MailTemplateData;

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

	/**
	 * @ORM\Column(name="id", type="integer")
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
	public function __construct($name, $domainId, MailTemplateData $mailTemplateData = null) {
		$this->name = $name;
		$this->domainId = $domainId;
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

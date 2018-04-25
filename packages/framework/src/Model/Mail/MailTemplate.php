<?php

namespace Shopsys\FrameworkBundle\Model\Mail;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="mail_templates",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="name_domain", columns={"name", "domain_id"})
 *     }
 * )
 * @ORM\Entity
 */
class MailTemplate
{
    const REGISTRATION_CONFIRM_NAME = 'registration_confirm';
    const RESET_PASSWORD_NAME = 'reset_password';
    const PERSONAL_DATA_ACCESS_NAME = 'personal_data_access';
    const PERSONAL_DATA_EXPORT_NAME = 'personal_data_export';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $bccEmail;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $subject;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $sendMail;

    /**
     * @param string $name
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData $mailTemplateData
     */
    public function __construct($name, $domainId, MailTemplateData $mailTemplateData)
    {
        $this->name = $name;
        $this->domainId = $domainId;
        $this->edit($mailTemplateData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData $mailTemplateData
     */
    public function edit(MailTemplateData $mailTemplateData)
    {
        $this->bccEmail = $mailTemplateData->bccEmail;
        $this->subject = $mailTemplateData->subject;
        $this->body = $mailTemplateData->body;
        $this->sendMail = $mailTemplateData->sendMail;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @return string|null
     */
    public function getBccEmail()
    {
        return $this->bccEmail;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return bool
     */
    public function isSendMail()
    {
        return $this->sendMail;
    }
}

<?php

namespace Shopsys\ShopBundle\Model\Mail;

class MailTemplateData
{
    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $bccEmail;

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
     * @var string[]
     */
    public $attachment;

    /**
     * @var bool
     */
    public $deleteAttachment;

    /**
     * @param string|null $name
     * @param string|null $subject
     * @param string|null $body
     * @param bool $sendMail
     * @param string|null $bccEmail
     * @param string[] $attachment
     */
    public function __construct(
        $name = null,
        $subject = null,
        $body = null,
        $sendMail = false,
        $bccEmail = null,
        array $attachment = []
    ) {
        $this->name = $name;
        $this->subject = $subject;
        $this->body = $body;
        $this->sendMail = $sendMail;
        $this->bccEmail = $bccEmail;
        $this->attachment = $attachment;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplate $mailTemplate
     */
    public function setFromEntity(MailTemplate $mailTemplate)
    {
        $this->name = $mailTemplate->getName();
        $this->bccEmail = $mailTemplate->getBccEmail();
        $this->subject = $mailTemplate->getSubject();
        $this->body = $mailTemplate->getBody();
        $this->sendMail = $mailTemplate->isSendMail();
        $this->attachment = [];
    }
}

<?php

namespace Shopsys\FrameworkBundle\Model\Mail;

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
     * @var bool|null
     */
    public $deleteAttachment;

    public function __construct()
    {
        $this->sendMail = false;
        $this->attachment = [];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate $mailTemplate
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

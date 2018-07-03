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
}

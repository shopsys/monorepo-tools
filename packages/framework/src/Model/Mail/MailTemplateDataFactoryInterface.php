<?php

namespace Shopsys\FrameworkBundle\Model\Mail;

interface MailTemplateDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData
     */
    public function create(): MailTemplateData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate $mailTemplate
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData
     */
    public function createFromMailTemplate(MailTemplate $mailTemplate): MailTemplateData;
}

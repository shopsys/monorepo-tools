<?php

namespace Shopsys\FrameworkBundle\Model\Mail;

interface MailTemplateFactoryInterface
{
    /**
     * @param string $name
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData $data
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplate
     */
    public function create(string $name, int $domainId, MailTemplateData $data): MailTemplate;
}

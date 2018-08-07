<?php

namespace Shopsys\FrameworkBundle\Model\Mail;

class MailTemplateFactory implements MailTemplateFactoryInterface
{
    /**
     * @param string $name
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData $data
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplate
     */
    public function create(string $name, int $domainId, MailTemplateData $data): MailTemplate
    {
        return new MailTemplate($name, $domainId, $data);
    }
}

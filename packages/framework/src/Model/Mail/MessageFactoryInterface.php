<?php

namespace Shopsys\FrameworkBundle\Model\Mail;

interface MessageFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate $template
     * @param mixed $personalData
     * @return \Shopsys\FrameworkBundle\Model\Mail\MessageData
     */
    public function createMessage(MailTemplate $template, $personalData);
}

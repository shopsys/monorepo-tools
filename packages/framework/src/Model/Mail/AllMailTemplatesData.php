<?php

namespace Shopsys\FrameworkBundle\Model\Mail;

class AllMailTemplatesData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData[]
     */
    public $orderStatusTemplates;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData
     */
    public $registrationTemplate;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData
     */
    public $resetPasswordTemplate;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData
     */
    public $personalDataAccessTemplate;

    /**
     * @var int
     */
    public $domainId;

    /**
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData[]
     */
    public function getAllTemplates()
    {
        $allTemplates = $this->orderStatusTemplates;
        $allTemplates[] = $this->registrationTemplate;
        $allTemplates[] = $this->resetPasswordTemplate;
        $allTemplates[] = $this->personalDataAccessTemplate;
        return $allTemplates;
    }
}

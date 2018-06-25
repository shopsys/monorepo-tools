<?php

namespace Shopsys\FrameworkBundle\Model\Mail;

class AllMailTemplatesData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData[]
     */
    public $orderStatusTemplates;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData|null
     */
    public $registrationTemplate;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData|null
     */
    public $resetPasswordTemplate;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData|null
     */
    public $personalDataAccessTemplate;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData|null
     */
    public $personalDataExportTemplate;

    /**
     * @var int|null
     */
    public $domainId;

    public function __construct()
    {
        $this->orderStatusTemplates = [];
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData[]
     */
    public function getAllTemplates()
    {
        $allTemplates = $this->orderStatusTemplates;
        $allTemplates[] = $this->registrationTemplate;
        $allTemplates[] = $this->resetPasswordTemplate;
        $allTemplates[] = $this->personalDataAccessTemplate;
        $allTemplates[] = $this->personalDataExportTemplate;
        return $allTemplates;
    }
}

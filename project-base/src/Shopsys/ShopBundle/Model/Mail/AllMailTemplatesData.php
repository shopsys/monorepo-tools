<?php

namespace Shopsys\ShopBundle\Model\Mail;

class AllMailTemplatesData
{

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailTemplateData[]
     */
    public $orderStatusTemplates;

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailTemplateData
     */
    public $registrationTemplate;

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailTemplateData
     */
    public $resetPasswordTemplate;

    /**
     * @var int
     */
    public $domainId;

    /**
     * @return \Shopsys\ShopBundle\Model\Mail\MailTemplateData[]
     */
    public function getAllTemplates() {
        $allTemplates = $this->orderStatusTemplates;
        $allTemplates[] = $this->registrationTemplate;
        $allTemplates[] = $this->resetPasswordTemplate;
        return $allTemplates;
    }
}

<?php

namespace Shopsys\FrameworkBundle\Model\Mail\Setting;

use Shopsys\FrameworkBundle\Component\Setting\Setting;

class MailSettingFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     */
    public function __construct(
        Setting $setting
    ) {
        $this->setting = $setting;
    }

    /**
     * @param int $domainId
     * @return string
     */
    public function getMainAdminMail($domainId)
    {
        return $this->setting->getForDomain(MailSetting::MAIN_ADMIN_MAIL, $domainId);
    }

    /**
     * @param int $domainId
     * @return string
     */
    public function getMainAdminMailName($domainId)
    {
        return $this->setting->getForDomain(MailSetting::MAIN_ADMIN_MAIL_NAME, $domainId);
    }

    /**
     * @param string $mainAdminMail
     * @param int $domainId
     */
    public function setMainAdminMail($mainAdminMail, $domainId)
    {
        $this->setting->setForDomain(MailSetting::MAIN_ADMIN_MAIL, $mainAdminMail, $domainId);
    }

    /**
     * @param string $mainAdminMailName
     * @param int $domainId
     */
    public function setMainAdminMailName($mainAdminMailName, $domainId)
    {
        $this->setting->setForDomain(MailSetting::MAIN_ADMIN_MAIL_NAME, $mainAdminMailName, $domainId);
    }
}

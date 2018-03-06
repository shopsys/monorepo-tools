<?php

namespace Shopsys\FrameworkBundle\Model\Heureka;

use Shopsys\FrameworkBundle\Component\Setting\Setting;

class HeurekaSetting
{
    const HEUREKA_API_KEY = 'heurekaApiKey';
    const HEUREKA_WIDGET_CODE = 'heurekaWidgetCode';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     */
    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    /**
     * @param int $domainId
     * @return string
     */
    public function getApiKeyByDomainId($domainId)
    {
        return $this->setting->getForDomain(self::HEUREKA_API_KEY, $domainId);
    }

    /**
     * @param int $domainId
     * @return string
     */
    public function getHeurekaShopCertificationWidgetByDomainId($domainId)
    {
        return $this->setting->getForDomain(self::HEUREKA_WIDGET_CODE, $domainId);
    }

    /**
     * @param string $apiKey
     * @param int $domainId
     */
    public function setApiKeyForDomain($apiKey, $domainId)
    {
        $this->setting->setForDomain(self::HEUREKA_API_KEY, $apiKey, $domainId);
    }

    /**
     * @param string $heurekaWidgetCode
     * @param int $domainId
     */
    public function setHeurekaShopCertificationWidgetForDomain($heurekaWidgetCode, $domainId)
    {
        $this->setting->setForDomain(self::HEUREKA_WIDGET_CODE, $heurekaWidgetCode, $domainId);
    }

    /**
     * @param int $domainId
     * @return bool
     */
    public function isHeurekaShopCertificationActivated($domainId)
    {
        return !empty($this->setting->getForDomain(self::HEUREKA_API_KEY, $domainId));
    }

    /**
     * @param int $domainId
     * @return bool
     */
    public function isHeurekaWidgetActivated($domainId)
    {
        return !empty($this->setting->getForDomain(self::HEUREKA_WIDGET_CODE, $domainId));
    }
}

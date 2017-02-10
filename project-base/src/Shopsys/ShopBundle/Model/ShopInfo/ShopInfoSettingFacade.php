<?php

namespace Shopsys\ShopBundle\Model\ShopInfo;

use Shopsys\ShopBundle\Component\Setting\Setting;

class ShopInfoSettingFacade {

    const SHOP_INFO_PHONE_NUMBER = 'shopInfoPhoneNumber';
    const SHOP_INFO_EMAIL = 'shopInfoEmail';
    const SHOP_INFO_PHONE_HOURS = 'shopInfoPhoneHours';

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    public function __construct(Setting $setting) {
        $this->setting = $setting;
    }

    /**
     * @param int $domainId
     * @return string
     */
    public function getPhoneNumber($domainId) {
        return $this->setting->getForDomain(self::SHOP_INFO_PHONE_NUMBER, $domainId);
    }

    /**
     * @param int $domainId
     * @return string
     */
    public function getEmail($domainId) {
        return $this->setting->getForDomain(self::SHOP_INFO_EMAIL, $domainId);
    }

    /**
     * @param int $domainId
     * @return string
     */
    public function getPhoneHours($domainId) {
        return $this->setting->getForDomain(self::SHOP_INFO_PHONE_HOURS, $domainId);
    }

    /**
     * @param string $value
     * @param int $domainId
     */
    public function setPhoneNumber($value, $domainId) {
        $this->setting->setForDomain(self::SHOP_INFO_PHONE_NUMBER, $value, $domainId);
    }

    /**
     * @param string $value
     * @param int $domainId
     */
    public function setEmail($value, $domainId) {
        $this->setting->setForDomain(self::SHOP_INFO_EMAIL, $value, $domainId);
    }

    /**
     * @param string $value
     * @param int $domainId
     */
    public function setPhoneHours($value, $domainId) {
        $this->setting->setForDomain(self::SHOP_INFO_PHONE_HOURS, $value, $domainId);
    }
}

<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\ShopInfo\ShopInfoSettingFacade;

class SettingValueShopInfoDataFixture extends AbstractReferenceFixture
{
    const SETTING_VALUES = [
        ShopInfoSettingFacade::SHOP_INFO_PHONE_NUMBER => '+420123456789',
        ShopInfoSettingFacade::SHOP_INFO_PHONE_HOURS => '(po-pá, 10:00 - 16:00)',
        ShopInfoSettingFacade::SHOP_INFO_EMAIL => 'no-reply@shopsys.cz',
    ];

    /** @var \Shopsys\FrameworkBundle\Component\Setting\Setting */
    private $setting;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     */
    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // Any previously executed data fixture using Setting (even transitively) would fill the Setting cache.
        // As EM identity map is cleared after each fixture we should clear the Setting cache before editing the values.
        $this->setting->clearCache();

        $domainId = 2;
        foreach (self::SETTING_VALUES as $key => $value) {
            $this->setting->setForDomain($key, $value, $domainId);
        }
    }
}

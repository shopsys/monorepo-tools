<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\ShopInfo\ShopInfoSettingFacade;

class SettingValueShopInfoDataFixture extends AbstractReferenceFixture
{
    /** @var \Shopsys\FrameworkBundle\Component\Setting\Setting */
    private $setting;

    const SETTING_VALUES = [
        ShopInfoSettingFacade::SHOP_INFO_PHONE_NUMBER => '+1-234-567-8989',
        ShopInfoSettingFacade::SHOP_INFO_PHONE_HOURS => '(Mon - Sat: 9 - 10 a.m. to 8 - 10 p.m.)',
        ShopInfoSettingFacade::SHOP_INFO_EMAIL => 'no-reply@shopsys.com',
    ];

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

        foreach (self::SETTING_VALUES as $key => $value) {
            $this->setting->setForDomain($key, $value, Domain::FIRST_DOMAIN_ID);
        }
    }
}

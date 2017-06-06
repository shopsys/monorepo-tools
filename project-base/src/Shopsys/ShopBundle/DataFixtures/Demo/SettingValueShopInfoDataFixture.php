<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use Shopsys\ShopBundle\Model\ShopInfo\ShopInfoSettingFacade;

class SettingValueShopInfoDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const SHOP_INFO_DEFAULT_VALUES = [
        ShopInfoSettingFacade::SHOP_INFO_EMAIL => 'no-reply@shopsys.com',
    ];
    const SHOP_INFO_VALUES_BY_LOCALE = [
        'en' => [
            ShopInfoSettingFacade::SHOP_INFO_PHONE_NUMBER => '+1-234-567-8989',
            ShopInfoSettingFacade::SHOP_INFO_PHONE_HOURS => '(Mon - Sat: 9 - 10 a.m. to 8 - 10 p.m.)',
        ],
        'cs' => [
            ShopInfoSettingFacade::SHOP_INFO_PHONE_NUMBER => '+420123456789',
            ShopInfoSettingFacade::SHOP_INFO_EMAIL => 'no-reply@shopsys.cz',
            ShopInfoSettingFacade::SHOP_INFO_PHONE_HOURS => '(po-pá, 10:00 - 16:00)',
        ],
    ];

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function load(ObjectManager $manager)
    {
        $setting = $this->get('shopsys.shop.component.setting');
        /* @var $setting \Shopsys\ShopBundle\Component\Setting\Setting */
        $domain = $this->get('shopsys.shop.component.domain');
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        // Any previously executed data fixture using Setting (even transitively) would fill the Setting cache.
        // As EM identity map is cleared after each fixture we should clear the Setting cache before editing the values.
        $setting->clearCache();

        foreach ($domain->getAll() as $domainConfig) {
            $shopInfoValues = $this->getShopInfoValuesByLocale($domainConfig->getLocale());

            $this->setShopInfoValuesForDomain($shopInfoValues, $setting, $domainConfig->getId());
        }
    }

    /**
     * @param string $locale
     * @return array
     */
    private function getShopInfoValuesByLocale($locale)
    {
        $shopInfoValues = self::SHOP_INFO_DEFAULT_VALUES;

        if (array_key_exists($locale, self::SHOP_INFO_VALUES_BY_LOCALE)) {
            $shopInfoValues = array_merge($shopInfoValues, self::SHOP_INFO_VALUES_BY_LOCALE[$locale]);
        }

        return $shopInfoValues;
    }

    /**
     * @param array $shopInfoValues
     * @param \Shopsys\ShopBundle\Component\Setting\Setting $setting
     * @param int $domainId
     */
    private function setShopInfoValuesForDomain(array $shopInfoValues, Setting $setting, $domainId)
    {
        foreach ($shopInfoValues as $key => $value) {
            $setting->setForDomain($key, $value, $domainId);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            SettingValueDataFixture::class,
        ];
    }
}

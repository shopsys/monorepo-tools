<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use Shopsys\ShopBundle\Model\Product\Brand\BrandData;
use Shopsys\ShopBundle\Model\Product\Brand\BrandFacade;

class BrandDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const BRAND_APPLE = 'brand_apple';
    const BRAND_CANON = 'brand_canon';
    const BRAND_LG = 'brand_lg';
    const BRAND_PHILIPS = 'brand_philips';
    const BRAND_SENCOR = 'brand_sencor';
    const BRAND_A4TECH = 'brand_a4tech';
    const BRAND_BROTHER = 'brand_brother';
    const BRAND_VERBATIM = 'brand_verbatim';
    const BRAND_DLINK = 'brand_dlink';
    const BRAND_DEFENDER = 'brand_defender';
    const BRAND_DELONGHI = 'brand_delonghi';
    const BRAND_GENIUS = 'brand_genius';
    const BRAND_GIGABYTE = 'brand_gigabyte';
    const BRAND_HP = 'brand_hp';
    const BRAND_HTC = 'brand_htc';
    const BRAND_JURA = 'brand_jura';
    const BRAND_LOGITECH = 'brand_logitech';
    const BRAND_MICROSOFT = 'brand_microsoft';
    const BRAND_SAMSUNG = 'brand_samsung';
    const BRAND_SONY = 'brand_sony';
    const BRAND_ORAVA = 'brand_orava';
    const BRAND_OLYMPUS = 'brand_olympus';
    const BRAND_HYUNDAI = 'brand_hyundai';
    const BRAND_NIKON = 'brand_nikon';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $brandFacade = $this->get('shopsys.shop.product.brand.brand_facade');
        /* @var $brandFacade \Shopsys\ShopBundle\Model\Product\Brand\BrandFacade */

        $brandEditDataFactory = $this->get('shopsys.shop.product.brand.brand_edit_data_factory');
        /* @var $brandEditDataFactory \Shopsys\ShopBundle\Model\Product\Brand\BrandEditDataFactory */

        $brandEditData = $brandEditDataFactory->createDefault();
        $brandData = $brandEditData->getBrandData();

        foreach ($this->getBrandNamesIndexedByBrandConstants() as $brandConstant => $brandName) {
            $brandData->name = $brandName;
            $brandData->descriptions = [
                'cs' => 'Toto je popis značky ' . $brandData->name . '.',
                'en' => 'This is description of brand ' . $brandData->name . '.',
            ];
            $brand = $brandFacade->create($brandEditData);
            $this->addReference($brandConstant, $brand);
        }
    }

    /**
     * @return string[]
     */
    private function getBrandNamesIndexedByBrandConstants()
    {
        return [
            self::BRAND_APPLE => 'Apple',
            self::BRAND_CANON => 'Canon',
            self::BRAND_LG => 'LG',
            self::BRAND_PHILIPS => 'Philips',
            self::BRAND_SENCOR => 'Sencor',
            self::BRAND_A4TECH => 'A4tech',
            self::BRAND_BROTHER => 'Brother',
            self::BRAND_VERBATIM => 'Verbatim',
            self::BRAND_DLINK => 'Dlink',
            self::BRAND_DEFENDER => 'Defender',
            self::BRAND_DELONGHI => 'DeLonghi',
            self::BRAND_GENIUS => 'Genius',
            self::BRAND_GIGABYTE => 'Gigabyte',
            self::BRAND_HP => 'HP',
            self::BRAND_HTC => 'HTC',
            self::BRAND_JURA => 'JURA',
            self::BRAND_LOGITECH => 'Logitech',
            self::BRAND_MICROSOFT => 'Microsoft',
            self::BRAND_SAMSUNG => 'Samsung',
            self::BRAND_SONY => 'SONY',
            self::BRAND_ORAVA => 'Orava',
            self::BRAND_OLYMPUS => 'Olympus',
            self::BRAND_HYUNDAI => 'Hyundai',
            self::BRAND_NIKON => 'Nikon',
        ];
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

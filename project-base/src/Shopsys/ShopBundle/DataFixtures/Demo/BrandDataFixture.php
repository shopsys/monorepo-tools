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

    const APPLE = 'brand_apple';
    const CANON = 'brand_canon';
    const LG = 'brand_lg';
    const PHILIPS = 'brand_philips';
    const SENCOR = 'brand_sencor';
    const A4TECH = 'brand_a4tech';
    const BROTHER = 'brand_brother';
    const VERBATIM = 'brand_verbatim';
    const DLINK = 'brand_dlink';
    const DEFENDER = 'brand_defender';
    const DELONGHI = 'brand_delonghi';
    const GENIUS = 'brand_genius';
    const GIGABYTE = 'brand_gigabyte';
    const HP = 'brand_hp';
    const HTC = 'brand_htc';
    const JURA = 'brand_jura';
    const LOGITECH = 'brand_logitech';
    const MICROSOFT = 'brand_microsoft';
    const SAMSUNG = 'brand_samsung';
    const SONY = 'brand_sony';
    const ORAVA = 'brand_orava';
    const OLYMPUS = 'brand_olympus';
    const HYUNDAI = 'brand_hyundai';
    const NIKON = 'brand_nikon';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $brandFacade = $this->get(BrandFacade::class);
        /* @var $brandFacade \Shopsys\ShopBundle\Model\Product\Brand\BrandFacade */

        $brandData = new BrandData();

        foreach ($this->getBrandNamesIndexedByBrandConstants() as $brandConstant => $brandName) {
            $brandData->name = $brandName;
            $brandData->descriptions = [
                'cs' => 'Toto je popis značky ' . $brandData->name . '.',
                'en' => 'This is description of brand ' . $brandData->name . '.',
            ];
            $brand = $brandFacade->create($brandData);
            $this->addReference($brandConstant, $brand);
        }
    }

    /**
     * @return string[]
     */
    private function getBrandNamesIndexedByBrandConstants() {
        return [
            self::APPLE => 'Apple',
            self::CANON => 'Canon',
            self::LG => 'LG',
            self::PHILIPS => 'Philips',
            self::SENCOR => 'Sencor',
            self::A4TECH => 'A4tech',
            self::BROTHER => 'Brother',
            self::VERBATIM => 'Verbatim',
            self::DLINK => 'Dlink',
            self::DEFENDER => 'Defender',
            self::DELONGHI => 'DeLonghi',
            self::GENIUS => 'Genius',
            self::GIGABYTE => 'Gigabyte',
            self::HP => 'HP',
            self::HTC => 'HTC',
            self::JURA => 'JURA',
            self::LOGITECH => 'Logitech',
            self::MICROSOFT => 'Microsoft',
            self::SAMSUNG => 'Samsung',
            self::SONY => 'SONY',
            self::ORAVA => 'Orava',
            self::OLYMPUS => 'Olympus',
            self::HYUNDAI => 'Hyundai',
            self::NIKON => 'Nikon',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies() {
        return [
            SettingValueDataFixture::class,
        ];
    }

}

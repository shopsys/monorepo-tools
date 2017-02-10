<?php

namespace Shopsys\ShopBundle\Component\DataFixture;

use Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\FlagDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\FulltextTriggersDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\PricingGroupDataFixture as DemoPricingGroupDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\UnitDataFixture as BaseUnitDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\BrandDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader;
use Shopsys\ShopBundle\DataFixtures\Demo\UnitDataFixture as DemoUnitDataFixture;
use Shopsys\ShopBundle\DataFixtures\DemoMultidomain\PricingGroupDataFixture as MultidomainPricingGroupDataFixture;

class ProductDataFixtureReferenceInjector
{

    /**
     * @param \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader $productDataFixtureLoader
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @param bool $onlyForFirstDomain
     */
    public function loadReferences(
        ProductDataFixtureLoader $productDataFixtureLoader,
        PersistentReferenceFacade $persistentReferenceFacade,
        $onlyForFirstDomain
    ) {
        $vats = $this->getVatReferences($persistentReferenceFacade);
        $availabilities = $this->getAvailabilityReferences($persistentReferenceFacade);
        $categories = $this->getCategoryReferences($persistentReferenceFacade);
        $flags = $this->getFlagReferences($persistentReferenceFacade);
        $brands = $this->getBrandReferences($persistentReferenceFacade);
        $units = $this->getUnitReferences($persistentReferenceFacade);
        if ($onlyForFirstDomain === true) {
            $pricingGroups = $this->getPricingGroupReferencesForFirstDomain($persistentReferenceFacade);
        } else {
            $pricingGroups = $this->getPricingGroupReferences($persistentReferenceFacade);
        }

        $productDataFixtureLoader->refreshCachedEntities(
            $vats,
            $availabilities,
            $categories,
            $flags,
            $brands,
            $units,
            $pricingGroups
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return string[]
     */
    private function getVatReferences(PersistentReferenceFacade $persistentReferenceFacade) {
        return [
            'high' => $persistentReferenceFacade->getReference(VatDataFixture::VAT_HIGH),
            'low' => $persistentReferenceFacade->getReference(VatDataFixture::VAT_LOW),
            'second_low' => $persistentReferenceFacade->getReference(VatDataFixture::VAT_SECOND_LOW),
            'zero' => $persistentReferenceFacade->getReference(VatDataFixture::VAT_ZERO),
        ];
    }

    /**
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return string[]
     */
    private function getAvailabilityReferences(PersistentReferenceFacade $persistentReferenceFacade) {
        return [
            'in-stock' => $persistentReferenceFacade->getReference(AvailabilityDataFixture::IN_STOCK),
            'out-of-stock' => $persistentReferenceFacade->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
            'on-request' => $persistentReferenceFacade->getReference(AvailabilityDataFixture::ON_REQUEST),
        ];
    }

    /**
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return string[]
     */
    private function getCategoryReferences(PersistentReferenceFacade $persistentReferenceFacade) {
        return [
            CategoryDataFixture::ELECTRONICS => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::ELECTRONICS
            ),
            CategoryDataFixture::TV => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::TV
            ),
            CategoryDataFixture::PHOTO => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::PHOTO
            ),
            CategoryDataFixture::PRINTERS => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::PRINTERS
            ),
            CategoryDataFixture::PC => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::PC
            ),
            CategoryDataFixture::PHONES => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::PHONES
            ),
            CategoryDataFixture::COFFEE => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::COFFEE
            ),
            CategoryDataFixture::BOOKS => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::BOOKS
            ),
            CategoryDataFixture::TOYS => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::TOYS
            ),
            CategoryDataFixture::GARDEN_TOOLS => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::GARDEN_TOOLS
            ),
            CategoryDataFixture::FOOD => $persistentReferenceFacade->getReference(
                CategoryDataFixture::PREFIX . CategoryDataFixture::FOOD
            ),
        ];
    }

    /**
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return string[]
     */
    private function getFlagReferences(PersistentReferenceFacade $persistentReferenceFacade) {
        return [
            'action' => $persistentReferenceFacade->getReference(FlagDataFixture::ACTION_PRODUCT),
            'new' => $persistentReferenceFacade->getReference(FlagDataFixture::NEW_PRODUCT),
            'top' => $persistentReferenceFacade->getReference(FlagDataFixture::TOP_PRODUCT),
        ];
    }

    /**
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return string[]
     */
    private function getBrandReferences(PersistentReferenceFacade $persistentReferenceFacade) {
        return [
            'apple' => $persistentReferenceFacade->getReference(BrandDataFixture::APPLE),
            'canon' => $persistentReferenceFacade->getReference(BrandDataFixture::CANON),
            'lg' => $persistentReferenceFacade->getReference(BrandDataFixture::LG),
            'philips' => $persistentReferenceFacade->getReference(BrandDataFixture::PHILIPS),
            'sencor' => $persistentReferenceFacade->getReference(BrandDataFixture::SENCOR),
            'a4tech' => $persistentReferenceFacade->getReference(BrandDataFixture::A4TECH),
            'brother' => $persistentReferenceFacade->getReference(BrandDataFixture::BROTHER),
            'verbatim' => $persistentReferenceFacade->getReference(BrandDataFixture::VERBATIM),
            'dlink' => $persistentReferenceFacade->getReference(BrandDataFixture::DLINK),
            'defender' => $persistentReferenceFacade->getReference(BrandDataFixture::DEFENDER),
            'delonghi' => $persistentReferenceFacade->getReference(BrandDataFixture::DELONGHI),
            'genius' => $persistentReferenceFacade->getReference(BrandDataFixture::GENIUS),
            'gigabyte' => $persistentReferenceFacade->getReference(BrandDataFixture::GIGABYTE),
            'hp' => $persistentReferenceFacade->getReference(BrandDataFixture::HP),
            'htc' => $persistentReferenceFacade->getReference(BrandDataFixture::HTC),
            'jura' => $persistentReferenceFacade->getReference(BrandDataFixture::JURA),
            'logitech' => $persistentReferenceFacade->getReference(BrandDataFixture::LOGITECH),
            'microsoft' => $persistentReferenceFacade->getReference(BrandDataFixture::MICROSOFT),
            'samsung' => $persistentReferenceFacade->getReference(BrandDataFixture::SAMSUNG),
            'sony' => $persistentReferenceFacade->getReference(BrandDataFixture::SONY),
            'orava' => $persistentReferenceFacade->getReference(BrandDataFixture::ORAVA),
            'olympus' => $persistentReferenceFacade->getReference(BrandDataFixture::OLYMPUS),
            'hyundai' => $persistentReferenceFacade->getReference(BrandDataFixture::HYUNDAI),
            'nikon' => $persistentReferenceFacade->getReference(BrandDataFixture::NIKON),
        ];
    }

    /**
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return string[]
     */
    private function getUnitReferences(PersistentReferenceFacade $persistentReferenceFacade) {
        return [
            'pcs' => $persistentReferenceFacade->getReference(BaseUnitDataFixture::PCS),
            'm3' => $persistentReferenceFacade->getReference(DemoUnitDataFixture::M3),
        ];
    }

    /**
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return string[]
     */
    private function getPricingGroupReferencesForFirstDomain(PersistentReferenceFacade $persistentReferenceFacade) {
        return [
            'ordinary_domain_1' => $persistentReferenceFacade->getReference(DemoPricingGroupDataFixture::ORDINARY_DOMAIN_1),
            'partner_domain_1' => $persistentReferenceFacade->getReference(DemoPricingGroupDataFixture::PARTNER_DOMAIN_1),
            'vip_domain_1' => $persistentReferenceFacade->getReference(DemoPricingGroupDataFixture::VIP_DOMAIN_1),
        ];
    }

    /**
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return string[]
     */
    private function getPricingGroupReferences(PersistentReferenceFacade $persistentReferenceFacade) {
        return [
            'ordinary_domain_1' => $persistentReferenceFacade->getReference(DemoPricingGroupDataFixture::ORDINARY_DOMAIN_1),
            'ordinary_domain_2' => $persistentReferenceFacade->getReference(MultidomainPricingGroupDataFixture::ORDINARY_DOMAIN_2),
            'partner_domain_1' => $persistentReferenceFacade->getReference(DemoPricingGroupDataFixture::PARTNER_DOMAIN_1),
            'vip_domain_1' => $persistentReferenceFacade->getReference(DemoPricingGroupDataFixture::VIP_DOMAIN_1),
            'vip_domain_2' => $persistentReferenceFacade->getReference(MultidomainPricingGroupDataFixture::VIP_DOMAIN_2),
        ];
    }

    /**
     * @return string[]
     */
    public static function getDependenciesForFirstDomain() {
        return [
            FulltextTriggersDataFixture::class,
            VatDataFixture::class,
            AvailabilityDataFixture::class,
            CategoryDataFixture::class,
            BrandDataFixture::class,
            BaseUnitDataFixture::class,
            DemoUnitDataFixture::class,
            DemoPricingGroupDataFixture::class,
        ];
    }

    /**
     * @return string[]
     */
    public static function getDependenciesForMultidomain() {
        return [
            MultidomainPricingGroupDataFixture::class,
        ];
    }
}

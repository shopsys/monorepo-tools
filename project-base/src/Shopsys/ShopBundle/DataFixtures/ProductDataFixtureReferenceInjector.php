<?php

namespace Shopsys\ShopBundle\DataFixtures;

use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Demo\AvailabilityDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\BrandDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\FlagDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\MultidomainPricingGroupDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader;
use Shopsys\ShopBundle\DataFixtures\Demo\UnitDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\VatDataFixture;

class ProductDataFixtureReferenceInjector
{
    /**
     * If you pass 2nd domain or higher, references will contain also first domain
     * If you pass higher domain than 2nd, data will be taken from 2nd CSV domain
     *
     * @param \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader $productDataFixtureLoader
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @param int $domainId
     */
    public function loadReferences(
        ProductDataFixtureLoader $productDataFixtureLoader,
        PersistentReferenceFacade $persistentReferenceFacade,
        int $domainId
    ) {
        $vats = $this->getVatReferences($persistentReferenceFacade);
        $availabilities = $this->getAvailabilityReferences($persistentReferenceFacade);
        $categories = $this->getCategoryReferences($persistentReferenceFacade);
        $flags = $this->getFlagReferences($persistentReferenceFacade);
        $brands = $this->getBrandReferences($persistentReferenceFacade);
        $units = $this->getUnitReferences($persistentReferenceFacade);
        if ($domainId === Domain::FIRST_DOMAIN_ID) {
            $pricingGroups = $this->getPricingGroupReferencesForFirstDomain($persistentReferenceFacade);
        } else {
            $pricingGroups = $this->getPricingGroupReferences($persistentReferenceFacade, $domainId);
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
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat[]
     */
    private function getVatReferences(PersistentReferenceFacade $persistentReferenceFacade)
    {
        return [
            'high' => $persistentReferenceFacade->getReference(VatDataFixture::VAT_HIGH),
            'low' => $persistentReferenceFacade->getReference(VatDataFixture::VAT_LOW),
            'second_low' => $persistentReferenceFacade->getReference(VatDataFixture::VAT_SECOND_LOW),
            'zero' => $persistentReferenceFacade->getReference(VatDataFixture::VAT_ZERO),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\Availability[]
     */
    private function getAvailabilityReferences(PersistentReferenceFacade $persistentReferenceFacade)
    {
        return [
            'in-stock' => $persistentReferenceFacade->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
            'out-of-stock' => $persistentReferenceFacade->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK),
            'on-request' => $persistentReferenceFacade->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    private function getCategoryReferences(PersistentReferenceFacade $persistentReferenceFacade)
    {
        return [
            'electronics' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_ELECTRONICS),
            'tv' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_TV),
            'photo' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_PHOTO),
            'printers' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_PRINTERS),
            'pc' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_PC),
            'phones' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_PHONES),
            'coffee' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_COFFEE),
            'books' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_BOOKS),
            'toys' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_TOYS),
            'garden_tools' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_GARDEN_TOOLS),
            'food' => $persistentReferenceFacade->getReference(CategoryDataFixture::CATEGORY_FOOD),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    private function getFlagReferences(PersistentReferenceFacade $persistentReferenceFacade)
    {
        return [
            'action' => $persistentReferenceFacade->getReference(FlagDataFixture::FLAG_ACTION_PRODUCT),
            'new' => $persistentReferenceFacade->getReference(FlagDataFixture::FLAG_NEW_PRODUCT),
            'top' => $persistentReferenceFacade->getReference(FlagDataFixture::FLAG_TOP_PRODUCT),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[]
     */
    private function getBrandReferences(PersistentReferenceFacade $persistentReferenceFacade)
    {
        return [
            'apple' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_APPLE),
            'canon' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_CANON),
            'lg' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_LG),
            'philips' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_PHILIPS),
            'sencor' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_SENCOR),
            'a4tech' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_A4TECH),
            'brother' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_BROTHER),
            'verbatim' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_VERBATIM),
            'dlink' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_DLINK),
            'defender' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_DEFENDER),
            'delonghi' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_DELONGHI),
            'genius' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_GENIUS),
            'gigabyte' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_GIGABYTE),
            'hp' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_HP),
            'htc' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_HTC),
            'jura' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_JURA),
            'logitech' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_LOGITECH),
            'microsoft' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_MICROSOFT),
            'samsung' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_SAMSUNG),
            'sony' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_SONY),
            'orava' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_ORAVA),
            'olympus' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_OLYMPUS),
            'hyundai' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_HYUNDAI),
            'nikon' => $persistentReferenceFacade->getReference(BrandDataFixture::BRAND_NIKON),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit[]
     */
    private function getUnitReferences(PersistentReferenceFacade $persistentReferenceFacade)
    {
        return [
            'pcs' => $persistentReferenceFacade->getReference(UnitDataFixture::UNIT_PIECES),
            'm3' => $persistentReferenceFacade->getReference(UnitDataFixture::UNIT_CUBIC_METERS),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup[]
     */
    private function getPricingGroupReferencesForFirstDomain(PersistentReferenceFacade $persistentReferenceFacade)
    {
        return [
            'ordinary_domain_1' => $persistentReferenceFacade->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1),
            'partner_domain_1' => $persistentReferenceFacade->getReference(PricingGroupDataFixture::PRICING_GROUP_PARTNER_DOMAIN_1),
            'vip_domain_1' => $persistentReferenceFacade->getReference(PricingGroupDataFixture::PRICING_GROUP_VIP_DOMAIN_1),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup[]
     */
    private function getPricingGroupReferences(PersistentReferenceFacade $persistentReferenceFacade, int $domainId)
    {
        return [
            'ordinary_domain_1' => $persistentReferenceFacade->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1),
            'ordinary_domain_2' => $persistentReferenceFacade->getReferenceForDomain(MultidomainPricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN, $domainId),
            'partner_domain_1' => $persistentReferenceFacade->getReference(PricingGroupDataFixture::PRICING_GROUP_PARTNER_DOMAIN_1),
            'vip_domain_1' => $persistentReferenceFacade->getReference(PricingGroupDataFixture::PRICING_GROUP_VIP_DOMAIN_1),
            'vip_domain_2' => $persistentReferenceFacade->getReferenceForDomain(MultidomainPricingGroupDataFixture::PRICING_GROUP_VIP_DOMAIN, $domainId),
        ];
    }

    /**
     * @return string[]
     */
    public static function getDependenciesForFirstDomain()
    {
        return [
            VatDataFixture::class,
            AvailabilityDataFixture::class,
            CategoryDataFixture::class,
            BrandDataFixture::class,
            UnitDataFixture::class,
            PricingGroupDataFixture::class,
        ];
    }
}

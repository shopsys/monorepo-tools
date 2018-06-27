<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class BrandDataFactory implements BrandDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    protected $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade
     */
    protected $brandFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        FriendlyUrlFacade $friendlyUrlFacade,
        BrandFacade $brandFacade,
        Domain $domain
    ) {
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->brandFacade = $brandFacade;
        $this->domain = $domain;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData
     */
    public function create(): BrandData
    {
        $brandData = new BrandData();
        $this->fillNew($brandData);

        return $brandData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    protected function fillNew(BrandData $brandData)
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $brandData->seoMetaDescriptions[$domainId] = null;
            $brandData->seoTitles[$domainId] = null;
            $brandData->seoH1s[$domainId] = null;
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData
     */
    public function createFromBrand(Brand $brand): BrandData
    {
        $brandData = new BrandData();
        $this->fillFromBrand($brandData, $brand);

        return $brandData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     */
    protected function fillFromBrand(BrandData $brandData, Brand $brand)
    {
        $brandData->name = $brand->getName();

        $translations = $brand->getTranslations();
        /* @var $translations \Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation[] */

        $brandData->descriptions = [];
        foreach ($translations as $translation) {
            $brandData->descriptions[$translation->getLocale()] = $translation->getDescription();
        }

        foreach ($this->domain->getAllIds() as $domainId) {
            $brandData->seoH1s[$domainId] = $brand->getSeoH1($domainId);
            $brandData->seoTitles[$domainId] = $brand->getSeoTitle($domainId);
            $brandData->seoMetaDescriptions[$domainId] = $brand->getSeoMetaDescription($domainId);

            $brandData->urls->mainFriendlyUrlsByDomainId[$domainId] =
                $this->friendlyUrlFacade->findMainFriendlyUrl(
                    $domainId,
                    'front_brand_detail',
                    $brand->getId()
                );
        }
    }
}

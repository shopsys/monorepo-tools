<?php

namespace Shopsys\ShopBundle\Model\Product\Brand;

use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\ShopBundle\Model\Product\Brand\Brand;
use Shopsys\ShopBundle\Model\Product\Brand\BrandData;

class BrandDataFactory
{
    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandFacade
     */
    private $brandFacade;

    public function __construct(
        FriendlyUrlFacade $friendlyUrlFacade,
        BrandFacade $brandFacade
    ) {
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->brandFacade = $brandFacade;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Brand\BrandData
     */
    public function createDefault()
    {
        return new BrandData();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\ShopBundle\Model\Product\Brand\BrandData
     */
    public function createFromBrand(Brand $brand)
    {
        $brandDomains = $this->brandFacade->getBrandDomainsByBrand($brand);

        $brandData = new BrandData();
        $brandData->name = $brand->getName();

        $translations = $brand->getTranslations();
        /* @var $translations \Shopsys\ShopBundle\Model\Product\Brand\BrandTranslation[]  */

        $brandData->descriptions = [];
        foreach ($translations as $translation) {
            $brandData->descriptions[$translation->getLocale()] = $translation->getDescription();
        }

        foreach ($brandDomains as $brandDomain) {
            $domainId = $brandDomain->getDomainId();

            $brandData->urls->mainFriendlyUrlsByDomainId[$domainId] =
                $this->friendlyUrlFacade->findMainFriendlyUrl(
                    $domainId,
                    'front_brand_detail',
                    $brand->getId()
                );
        }

        return $brandData;
    }
}

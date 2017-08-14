<?php

namespace Shopsys\ShopBundle\Model\Product\Brand;

use Shopsys\ShopBundle\Model\Product\Brand\Brand;
use Shopsys\ShopBundle\Model\Product\Brand\BrandData;

class BrandEditDataFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandDataFactory
     */
    private $brandDataFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandRepository
     */
    private $brandRepository;

    public function __construct(
        BrandDataFactory $brandDataFactory,
        BrandRepository $brandRepository
    ) {
        $this->brandDataFactory = $brandDataFactory;
        $this->brandRepository = $brandRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\ShopBundle\Model\Product\Brand\BrandEditData
     */
    public function createFromBrand(Brand $brand)
    {
        $brandEditData = new BrandEditData();
        $brandEditData->brandData = $this->brandDataFactory->createFromBrand($brand);

        $this->setMultidomainData($brand, $brandEditData);

        return $brandEditData;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandEditData $brandEditData
     */
    private function setMultidomainData(Brand $brand, BrandEditData $brandEditData)
    {
        $brandDomains = $this->brandRepository->getBrandDomainsByBrand($brand);
        foreach ($brandDomains as $brandDomain) {
            $domainId = $brandDomain->getDomainId();

            $brandEditData->seoTitles[$domainId] = $brandDomain->getSeoTitle();
            $brandEditData->seoMetaDescriptions[$domainId] = $brandDomain->getSeoMetaDescription();
            $brandEditData->seoH1s[$domainId] = $brandDomain->getSeoH1();
        }
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Brand\BrandEditData
     */
    public function createDefault()
    {
        $brandData = $this->brandDataFactory->createDefault();
        $brandEditData = new BrandEditData($brandData);

        return $brandEditData;
    }
}

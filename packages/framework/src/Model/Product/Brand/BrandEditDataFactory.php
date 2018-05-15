<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

class BrandEditDataFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDataFactory
     */
    protected $brandDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandRepository
     */
    protected $brandRepository;

    public function __construct(
        BrandDataFactory $brandDataFactory,
        BrandRepository $brandRepository
    ) {
        $this->brandDataFactory = $brandDataFactory;
        $this->brandRepository = $brandRepository;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandEditData
     */
    public function createFromBrand(Brand $brand)
    {
        $brandEditData = new BrandEditData();
        $brandEditData->brandData = $this->brandDataFactory->createFromBrand($brand);

        $this->setMultidomainData($brand, $brandEditData);

        return $brandEditData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandEditData $brandEditData
     */
    protected function setMultidomainData(Brand $brand, BrandEditData $brandEditData)
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
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandEditData
     */
    public function createDefault()
    {
        $brandData = $this->brandDataFactory->createDefault();

        return new BrandEditData($brandData);
    }
}

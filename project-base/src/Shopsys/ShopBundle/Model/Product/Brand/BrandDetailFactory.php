<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

class BrandDetailFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandRepository
     */
    private $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDetail
     */
    public function getDetailForBrand(Brand $brand)
    {
        return new BrandDetail($brand, $this);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain[]
     */
    public function getBrandDomainsIndexedByDomainId(Brand $brand)
    {
        return $this->brandRepository->getBrandDomainsIndexedByDomain($brand);
    }
}

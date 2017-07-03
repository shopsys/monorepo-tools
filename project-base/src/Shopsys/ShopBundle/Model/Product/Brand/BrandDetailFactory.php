<?php

namespace Shopsys\ShopBundle\Model\Product\Brand;

use Shopsys\ShopBundle\Model\Product\Brand\Brand;
use Shopsys\ShopBundle\Model\Product\Brand\BrandRepository;

class BrandDetailFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandRepository
     */
    private $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\ShopBundle\Model\Product\Brand\BrandDetail
     */
    public function getDetailForBrand(Brand $brand)
    {
        return new BrandDetail($brand, $this);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\ShopBundle\Model\Product\Brand\BrandDomain[]
     */
    public function getBrandDomainsIndexedByDomainId(Brand $brand)
    {
        return $this->brandRepository->getBrandDomainsIndexedByDomain($brand);
    }
}

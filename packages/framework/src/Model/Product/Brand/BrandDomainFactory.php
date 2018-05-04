<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

class BrandDomainFactory implements BrandDomainFactoryInterface
{

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @param int $domainId
     */
    public function create(Brand $brand, int $domainId): BrandDomain
    {
        return new BrandDomain($brand, $domainId);
    }
}

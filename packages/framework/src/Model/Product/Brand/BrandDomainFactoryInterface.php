<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

interface BrandDomainFactoryInterface
{

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @param int $domainId
     */
    public function create(Brand $brand, int $domainId): BrandDomain;
}

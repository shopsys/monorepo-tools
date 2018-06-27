<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

interface BrandDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData
     */
    public function create(): BrandData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData
     */
    public function createFromBrand(Brand $brand): BrandData;
}

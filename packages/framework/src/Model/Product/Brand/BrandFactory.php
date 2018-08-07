<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

class BrandFactory implements BrandFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand
     */
    public function create(BrandData $data): Brand
    {
        return new Brand($data);
    }
}

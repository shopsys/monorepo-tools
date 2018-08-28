<?php

namespace Shopsys\ShopBundle\Model\Product\Brand;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand as BaseBrand;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandData as BaseBrandData;

/**
 * @ORM\Table(name="brands")
 * @ORM\Entity
 */
class Brand extends BaseBrand
{
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandData $brandData
     */
    public function __construct(BaseBrandData $brandData)
    {
        parent::__construct($brandData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandData $brandData
     */
    public function edit(BaseBrandData $brandData)
    {
        parent::edit($brandData);
    }
}

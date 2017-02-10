<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Pricing\Price;

class ProductSellingPrice
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup
     */
    private $pricingGroup;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price
     */
    private $sellingPrice;

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $sellingPrice
     */
    public function __construct(PricingGroup $pricingGroup, Price $sellingPrice)
    {
        $this->pricingGroup = $pricingGroup;
        $this->sellingPrice = $sellingPrice;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup
     */
    public function getPricingGroup()
    {
        return $this->pricingGroup;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function getSellingPrice()
    {
        return $this->sellingPrice;
    }
}

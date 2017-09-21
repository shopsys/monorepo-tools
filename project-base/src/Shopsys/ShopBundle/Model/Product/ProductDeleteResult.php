<?php

namespace Shopsys\ShopBundle\Model\Product;

class ProductDeleteResult
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product[]
     */
    private $productsForRecalculations;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $productsForRecalculations
     */
    public function __construct(array $productsForRecalculations = [])
    {
        $this->productsForRecalculations = $productsForRecalculations;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public function getProductsForRecalculations()
    {
        return $this->productsForRecalculations;
    }
}

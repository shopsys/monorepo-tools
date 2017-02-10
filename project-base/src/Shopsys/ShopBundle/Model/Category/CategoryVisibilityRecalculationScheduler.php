<?php

namespace Shopsys\ShopBundle\Model\Category;

use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade;

class CategoryVisibilityRecalculationScheduler
{
    /**
     * @var bool
     */
    private $recalculate = false;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade
     */
    private $productVisibilityFacade;

    public function __construct(ProductVisibilityFacade $productVisibilityFacade)
    {
        $this->productVisibilityFacade = $productVisibilityFacade;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     */
    public function scheduleRecalculation(Category $category)
    {
        $this->recalculate = true;
        $this->productVisibilityFacade->markProductsForRecalculationAffectedByCategory($category);
    }

    public function scheduleRecalculationWithoutDependencies()
    {
        $this->recalculate = true;
    }

    /**
     * @return bool
     */
    public function isRecalculationScheduled()
    {
        return $this->recalculate;
    }
}

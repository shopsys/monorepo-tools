<?php

namespace Shopsys\FrameworkBundle\Model\Category;

use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade;

class CategoryVisibilityRecalculationScheduler
{
    /**
     * @var bool
     */
    protected $recalculate = false;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade
     */
    protected $productVisibilityFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade $productVisibilityFacade
     */
    public function __construct(ProductVisibilityFacade $productVisibilityFacade)
    {
        $this->productVisibilityFacade = $productVisibilityFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
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

<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductVisibilityFacade
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository
     */
    private $productVisibilityRepository;

    /**
     * @var bool
     */
    private $recalcVisibilityForMarked = false;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository
     */
    public function __construct(ProductVisibilityRepository $productVisibilityRepository)
    {
        $this->productVisibilityRepository = $productVisibilityRepository;
    }

    public function refreshProductsVisibilityForMarkedDelayed()
    {
        $this->recalcVisibilityForMarked = true;
    }

    public function refreshProductsVisibility()
    {
        $this->productVisibilityRepository->refreshProductsVisibility();
    }

    public function refreshProductsVisibilityForMarked()
    {
        $this->productVisibilityRepository->refreshProductsVisibility(true);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     */
    public function markProductsForRecalculationAffectedByCategory(Category $category)
    {
        $this->productVisibilityRepository->markProductsForRecalculationAffectedByCategory($category);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->recalcVisibilityForMarked) {
            $this->refreshProductsVisibilityForMarked();
        }
    }
}

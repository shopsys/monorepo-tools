<?php

namespace Shopsys\ShopBundle\Model\Breadcrumb;

use Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbResolver;
use Shopsys\ShopBundle\Model\Article\ArticleBreadcrumbGenerator;
use Shopsys\ShopBundle\Model\Breadcrumb\SimpleBreadcrumbGenerator;
use Shopsys\ShopBundle\Model\Category\CategoryBreadcrumbGenerator;
use Shopsys\ShopBundle\Model\Product\Brand\BrandBreadcrumbGenerator;
use Shopsys\ShopBundle\Model\Product\ProductBreadcrumbGenerator;

class FrontBreadcrumbResolverFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Breadcrumb\SimpleBreadcrumbGenerator[]
     */
    private $breadcrumbGenerators;

    public function __construct(
        ArticleBreadcrumbGenerator $articleBreadcrumbGenerator,
        CategoryBreadcrumbGenerator $categoryBreadcrumbGenerator,
        ProductBreadcrumbGenerator $productBreadcrumbGenerator,
        SimpleBreadcrumbGenerator $frontBreadcrumbGenerator,
        BrandBreadcrumbGenerator $brandBreadcrumbGenerator,
        ErrorPageBreadcrumbGenerator $errorPageBreadcrumbGenerator
    ) {
        $this->breadcrumbGenerators = [
            $articleBreadcrumbGenerator,
            $categoryBreadcrumbGenerator,
            $productBreadcrumbGenerator,
            $frontBreadcrumbGenerator,
            $brandBreadcrumbGenerator,
            $errorPageBreadcrumbGenerator,
        ];
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbResolver
     */
    public function create()
    {
        $frontBreadcrumbResolver = new BreadcrumbResolver();
        foreach ($this->breadcrumbGenerators as $breadcrumbGenerator) {
            $frontBreadcrumbResolver->registerGenerator($breadcrumbGenerator);
        }

        return $frontBreadcrumbResolver;
    }
}

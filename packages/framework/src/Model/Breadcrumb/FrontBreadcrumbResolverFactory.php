<?php

namespace Shopsys\FrameworkBundle\Model\Breadcrumb;

use Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbResolver;
use Shopsys\FrameworkBundle\Model\Article\ArticleBreadcrumbGenerator;
use Shopsys\FrameworkBundle\Model\Category\CategoryBreadcrumbGenerator;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataBreadcrumbResolverFactory;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandBreadcrumbGenerator;
use Shopsys\FrameworkBundle\Model\Product\ProductBreadcrumbGenerator;

class FrontBreadcrumbResolverFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Breadcrumb\SimpleBreadcrumbGenerator[]
     */
    private $breadcrumbGenerators;

    public function __construct(
        ArticleBreadcrumbGenerator $articleBreadcrumbGenerator,
        CategoryBreadcrumbGenerator $categoryBreadcrumbGenerator,
        ProductBreadcrumbGenerator $productBreadcrumbGenerator,
        SimpleBreadcrumbGenerator $frontBreadcrumbGenerator,
        BrandBreadcrumbGenerator $brandBreadcrumbGenerator,
        ErrorPageBreadcrumbGenerator $errorPageBreadcrumbGenerator,
        PersonalDataBreadcrumbResolverFactory $personalDataBreadcrumbResolverFactory
    ) {
        $this->breadcrumbGenerators = [
            $articleBreadcrumbGenerator,
            $categoryBreadcrumbGenerator,
            $productBreadcrumbGenerator,
            $frontBreadcrumbGenerator,
            $brandBreadcrumbGenerator,
            $errorPageBreadcrumbGenerator,
            $personalDataBreadcrumbResolverFactory,
        ];
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbResolver
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

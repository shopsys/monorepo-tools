<?php

namespace Shopsys\FrameworkBundle\Model\Breadcrumb;

use Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbResolver;
use Shopsys\FrameworkBundle\Model\Article\ArticleBreadcrumbGenerator;
use Shopsys\FrameworkBundle\Model\Category\CategoryBreadcrumbGenerator;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataBreadcrumbGenerator;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandBreadcrumbGenerator;
use Shopsys\FrameworkBundle\Model\Product\ProductBreadcrumbGenerator;

class FrontBreadcrumbResolverFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Breadcrumb\SimpleBreadcrumbGenerator[]
     */
    protected $breadcrumbGenerators;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleBreadcrumbGenerator $articleBreadcrumbGenerator
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryBreadcrumbGenerator $categoryBreadcrumbGenerator
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductBreadcrumbGenerator $productBreadcrumbGenerator
     * @param \Shopsys\FrameworkBundle\Model\Breadcrumb\SimpleBreadcrumbGenerator $frontBreadcrumbGenerator
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandBreadcrumbGenerator $brandBreadcrumbGenerator
     * @param \Shopsys\FrameworkBundle\Model\Breadcrumb\ErrorPageBreadcrumbGenerator $errorPageBreadcrumbGenerator
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataBreadcrumbGenerator $personalDataBreadcrumbGenerator
     */
    public function __construct(
        ArticleBreadcrumbGenerator $articleBreadcrumbGenerator,
        CategoryBreadcrumbGenerator $categoryBreadcrumbGenerator,
        ProductBreadcrumbGenerator $productBreadcrumbGenerator,
        SimpleBreadcrumbGenerator $frontBreadcrumbGenerator,
        BrandBreadcrumbGenerator $brandBreadcrumbGenerator,
        ErrorPageBreadcrumbGenerator $errorPageBreadcrumbGenerator,
        PersonalDataBreadcrumbGenerator $personalDataBreadcrumbGenerator
    ) {
        $this->breadcrumbGenerators = [
            $articleBreadcrumbGenerator,
            $categoryBreadcrumbGenerator,
            $productBreadcrumbGenerator,
            $frontBreadcrumbGenerator,
            $brandBreadcrumbGenerator,
            $errorPageBreadcrumbGenerator,
            $personalDataBreadcrumbGenerator,
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

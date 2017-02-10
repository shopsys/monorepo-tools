<?php

namespace Shopsys\ShopBundle\Model\Breadcrumb;

use Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbResolver;
use Shopsys\ShopBundle\Model\Article\ArticleBreadcrumbGenerator;
use Shopsys\ShopBundle\Model\Breadcrumb\FrontBreadcrumbGenerator;
use Shopsys\ShopBundle\Model\Category\CategoryBreadcrumbGenerator;
use Shopsys\ShopBundle\Model\Product\Brand\BrandBreadcrumbGenerator;
use Shopsys\ShopBundle\Model\Product\ProductBreadcrumbGenerator;

class FrontBreadcrumbResolverFactory {

    /**
     * @var \Shopsys\ShopBundle\Model\Breadcrumb\FrontBreadcrumbGenerator
     */
    private $frontBreadcrumbGenerator;

    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticleBreadcrumbGenerator;
     */
    private $articleBreadcrumbGenerator;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryBreadcrumbGenerator;
     */
    private $categoryBreadcrumbGenerator;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductBreadcrumbGenerator
     */
    private $productBreadcrumbGenerator;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandBreadcrumbGenerator
     */
    private $brandBreadcrumbGenearator;

    public function __construct(
        ArticleBreadcrumbGenerator $articleBreadcrumbGenerator,
        CategoryBreadcrumbGenerator $categoryBreadcrumbGenerator,
        ProductBreadcrumbGenerator $productBreadcrumbGenerator,
        FrontBreadcrumbGenerator $frontBreadcrumbGenerator,
        BrandBreadcrumbGenerator $brandBreadcrumbGenerator
    ) {
        $this->articleBreadcrumbGenerator = $articleBreadcrumbGenerator;
        $this->categoryBreadcrumbGenerator = $categoryBreadcrumbGenerator;
        $this->productBreadcrumbGenerator = $productBreadcrumbGenerator;
        $this->frontBreadcrumbGenerator = $frontBreadcrumbGenerator;
        $this->brandBreadcrumbGenearator = $brandBreadcrumbGenerator;
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbResolver
     */
    public function create() {
        $frontBreadcrumbResolver = new BreadcrumbResolver();
        $frontBreadcrumbResolver->registerGenerator('front_article_detail', $this->articleBreadcrumbGenerator);
        $frontBreadcrumbResolver->registerGenerator('front_product_list', $this->categoryBreadcrumbGenerator);
        $frontBreadcrumbResolver->registerGenerator('front_product_detail', $this->productBreadcrumbGenerator);
        $frontBreadcrumbResolver->registerGenerator('front_brand_detail', $this->brandBreadcrumbGenearator);

        $this->frontBreadcrumbGenerator->registerAll($frontBreadcrumbResolver);

        return $frontBreadcrumbResolver;
    }

}

<?php

namespace SS6\ShopBundle\Model\Breadcrumb;

use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbResolver;
use SS6\ShopBundle\Model\Article\ArticleBreadcrumbGenerator;
use SS6\ShopBundle\Model\Breadcrumb\FrontBreadcrumbGenerator;
use SS6\ShopBundle\Model\Category\CategoryBreadcrumbGenerator;
use SS6\ShopBundle\Model\Product\Brand\BrandBreadcrumbGenerator;
use SS6\ShopBundle\Model\Product\ProductBreadcrumbGenerator;

class FrontBreadcrumbResolverFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Breadcrumb\FrontBreadcrumbGenerator
	 */
	private $frontBreadcrumbGenerator;

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleBreadcrumbGenerator;
	 */
	private $articleBreadcrumbGenerator;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryBreadcrumbGenerator;
	 */
	private $categoryBreadcrumbGenerator;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductBreadcrumbGenerator
	 */
	private $productBreadcrumbGenerator;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\BrandBreadcrumbGenerator
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
	 * @return \SS6\ShopBundle\Component\Breadcrumb\BreadcrumbResolver
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

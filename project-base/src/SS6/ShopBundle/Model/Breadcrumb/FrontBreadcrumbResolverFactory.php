<?php

namespace SS6\ShopBundle\Model\Breadcrumb;

use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbResolver;
use SS6\ShopBundle\Model\Article\ArticleBreadcrumbGenerator;
use SS6\ShopBundle\Model\Category\CategoryBreadcrumbGenerator;
use SS6\ShopBundle\Model\Product\ProductBreadcrumbGenerator;

class FrontBreadcrumbResolverFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleBreadcrumbGenerator;
	 */
	private $articleBreadcrumbGenerator;

	/**
	 * @var \SS6\ShopBundle\Model\Article\CategoryBreadcrumbGenerator;
	 */
	private $categoryBreadcrumbGenerator;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductBreadcrumbGenerator
	 */
	private $productBreadcrumbGenerator;

	public function __construct(
		ArticleBreadcrumbGenerator $articleBreadcrumbGenerator,
		CategoryBreadcrumbGenerator $categoryBreadcrumbGenerator,
		ProductBreadcrumbGenerator $productBreadcrumbGenerator
	) {
		$this->articleBreadcrumbGenerator = $articleBreadcrumbGenerator;
		$this->categoryBreadcrumbGenerator = $categoryBreadcrumbGenerator;
		$this->productBreadcrumbGenerator = $productBreadcrumbGenerator;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Breadcrumb\BreadcrumbResolver
	 */
	public function create() {
		$frontBreadcrumbResolver = new BreadcrumbResolver();
		$frontBreadcrumbResolver->registerGenerator('front_article_detail', $this->articleBreadcrumbGenerator);
		$frontBreadcrumbResolver->registerGenerator('front_product_list', $this->categoryBreadcrumbGenerator);
		$frontBreadcrumbResolver->registerGenerator('front_product_detail', $this->productBreadcrumbGenerator);

		return $frontBreadcrumbResolver;
	}

}

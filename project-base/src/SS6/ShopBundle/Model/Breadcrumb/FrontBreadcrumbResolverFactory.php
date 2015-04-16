<?php

namespace SS6\ShopBundle\Model\Breadcrumb;

use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbResolver;
use SS6\ShopBundle\Model\Article\ArticleBreadcrumbGenerator;

class FrontBreadcrumbResolverFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleBreadcrumbGenerator;
	 */
	private $articleBreadcrumbGenerator;

	public function __construct(
		ArticleBreadcrumbGenerator $articleBreadcrumbGenerator
	) {
		$this->articleBreadcrumbGenerator = $articleBreadcrumbGenerator;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Breadcrumb\BreadcrumbResolver
	 */
	public function create() {
		$frontBreadcrumbResolver = new BreadcrumbResolver();
		$frontBreadcrumbResolver->registerGenerator('front_article_detail', $this->articleBreadcrumbGenerator);

		return $frontBreadcrumbResolver;
	}

}

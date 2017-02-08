<?php

namespace SS6\ShopBundle\Model\Article;

use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbItem;

class ArticleBreadcrumbGenerator implements BreadcrumbGeneratorInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleRepository
	 */
	private $articleRepository;

	public function __construct(ArticleRepository $articleRepository) {
		$this->articleRepository = $articleRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBreadcrumbItems($routeName, array $routeParameters = []) {
		$article = $this->articleRepository->getById($routeParameters['id']);

		return [
			new BreadcrumbItem($article->getName()),
		];
	}

}

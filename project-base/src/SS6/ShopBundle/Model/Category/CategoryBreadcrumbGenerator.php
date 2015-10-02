<?php

namespace SS6\ShopBundle\Model\Category;

use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbItem;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Category\CategoryRepository;

class CategoryBreadcrumbGenerator implements BreadcrumbGeneratorInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(
		CategoryRepository $categoryRepository,
		Domain $domain
	) {
		$this->categoryRepository = $categoryRepository;
		$this->domain = $domain;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBreadcrumbItems($routeName, array $routeParameters = []) {
		$category = $this->categoryRepository->getById($routeParameters['id']);

		$categoriesInPath = $this->categoryRepository->getVisibleCategoriesInPathFromRootOnDomain(
			$category,
			$this->domain->getId()
		);

		$breadcrumbItems = [];
		foreach ($categoriesInPath as $categoryInPath) {
			if ($categoryInPath !== $category) {
				$breadcrumbItems[] = new BreadcrumbItem(
					$categoryInPath->getName(),
					$routeName,
					['id' => $categoryInPath->getId()]
				);
			} else {
				$breadcrumbItems[] = new BreadcrumbItem(
					$categoryInPath->getName()
				);
			}
		}

		return $breadcrumbItems;
	}

}

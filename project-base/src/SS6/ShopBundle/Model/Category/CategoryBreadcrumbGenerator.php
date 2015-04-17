<?php

namespace SS6\ShopBundle\Model\Category;

use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbItem;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Domain\Domain;

class CategoryBreadcrumbGenerator implements BreadcrumbGeneratorInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
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

		$categoryPath = $this->categoryRepository->getVisibleCategoryPathFromRootOnDomain(
			$category,
			$this->domain->getId()
		);

		$breadcrumbItems = [];
		foreach ($categoryPath as $categoryInPath) {
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

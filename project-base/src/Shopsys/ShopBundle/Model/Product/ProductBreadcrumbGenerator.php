<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbItem;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Category\CategoryRepository;

class ProductBreadcrumbGenerator implements BreadcrumbGeneratorInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(
		ProductRepository $productRepository,
		CategoryRepository $categoryRepository,
		CategoryFacade $categoryFacade,
		Domain $domain
	) {
		$this->productRepository = $productRepository;
		$this->categoryRepository = $categoryRepository;
		$this->categoryFacade = $categoryFacade;
		$this->domain = $domain;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBreadcrumbItems($routeName, array $routeParameters = []) {
		$product = $this->productRepository->getById($routeParameters['id']);

		$productMainCategory = $this->categoryRepository->getProductMainCategoryOnDomain(
			$product,
			$this->domain->getId()
		);

		$breadcrumbItems = $this->getCategoryBreadcrumbItems($productMainCategory);

		$breadcrumbItems[] = new BreadcrumbItem(
			$product->getName()
		);

		return $breadcrumbItems;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Component\Breadcrumb\BreadcrumbItem[]
	 */
	private function getCategoryBreadcrumbItems(Category $category) {
		$categoriesInPath = $this->categoryRepository->getVisibleCategoriesInPathFromRootOnDomain(
			$category,
			$this->domain->getId()
		);

		$breadcrumbItems = [];
		foreach ($categoriesInPath as $categoryInPath) {
			$breadcrumbItems[] = new BreadcrumbItem(
				$categoryInPath->getName(),
				'front_product_list',
				['id' => $categoryInPath->getId()]
			);
		}

		return $breadcrumbItems;
	}

}

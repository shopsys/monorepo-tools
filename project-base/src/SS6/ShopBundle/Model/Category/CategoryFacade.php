<?php

namespace SS6\ShopBundle\Model\Category;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use SS6\ShopBundle\Model\Category\CategoryData;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Category\CategoryService;
use SS6\ShopBundle\Model\Category\CategoryVisibilityRecalculationScheduler;
use SS6\ShopBundle\Model\Category\Detail\CategoryDetailFactory;
use SS6\ShopBundle\Model\Product\Product;

class CategoryFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryService
	 */
	private $categoryService;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryVisibilityRecalculationScheduler
	 */
	private $categoryVisibilityRecalculationScheduler;

	/**
	 * @var \SS6\ShopBundle\Model\Category\Detail\CategoryDetailFactory
	 */
	private $categoryDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	public function __construct(
		EntityManager $em,
		CategoryRepository $categoryRepository,
		CategoryService $categoryService,
		Domain $domain,
		CategoryVisibilityRecalculationScheduler $categoryVisibilityRecalculationScheduler,
		CategoryDetailFactory $categoryDetailFactory,
		FriendlyUrlFacade $friendlyUrlFacade
	) {
		$this->em = $em;
		$this->categoryRepository = $categoryRepository;
		$this->categoryService = $categoryService;
		$this->domain = $domain;
		$this->categoryVisibilityRecalculationScheduler = $categoryVisibilityRecalculationScheduler;
		$this->categoryDetailFactory = $categoryDetailFactory;
		$this->friendlyUrlFacade = $friendlyUrlFacade;
	}

	/**
	 * @param int $categoryId
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function getById($categoryId) {
		return $this->categoryRepository->getById($categoryId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\CategoryData $categoryData
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function create(CategoryData $categoryData) {
		try {
			$this->em->beginTransaction();

			$rootCategory = $this->categoryRepository->getRootCategory();
			$category = $this->categoryService->create($categoryData, $rootCategory);
			$this->em->persist($category);
			$this->em->flush();
			$this->createCategoryDomains($category, $this->domain->getAll());
			$this->friendlyUrlFacade->createFriendlyUrls('front_product_list', $category->getId(), $category->getNames());
			$this->em->flush();

			$this->categoryVisibilityRecalculationScheduler->scheduleRecalculation();

			$this->em->commit();

			return $category;
		} catch (\Exception $ex) {
			$this->em->rollback();
			throw $ex;
		}
	}

	/**
	 * @param int $categoryId
	 * @param \SS6\ShopBundle\Model\Category\CategoryData $categoryData
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function edit($categoryId, CategoryData $categoryData) {
		try {
			$this->em->beginTransaction();

			$rootCategory = $this->categoryRepository->getRootCategory();
			$category = $this->categoryRepository->getById($categoryId);
			$this->categoryService->edit($category, $categoryData, $rootCategory);
			$this->refreshCategoryDomains($category, $categoryData->hiddenOnDomains);
			$this->friendlyUrlFacade->saveUrlListFormData('front_product_list', $category->getId(), $categoryData->urls);
			$this->friendlyUrlFacade->createFriendlyUrls('front_product_list', $category->getId(), $category->getNames());
			$this->em->flush();

			$this->categoryVisibilityRecalculationScheduler->scheduleRecalculation();

			$this->em->commit();

			return $category;
		} catch (\Exception $ex) {
			$this->em->rollback();
			throw $ex;
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
	 */
	private function createCategoryDomains(Category $category, array $domainConfigs) {
		foreach ($domainConfigs as $domainConfig) {
			$categoryDomain = new CategoryDomain($category, $domainConfig->getId());
			$this->em->persist($categoryDomain);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param int[] $hiddenOnDomainData
	 */
	private function refreshCategoryDomains(Category $category, array $hiddenOnDomainData) {
		$categoryDomains = $this->categoryRepository->getCategoryDomainsByCategory($category);
		foreach ($categoryDomains as $categoryDomain) {
			if (in_array($categoryDomain->getDomainId(), $hiddenOnDomainData)) {
				$categoryDomain->setHidden(true);
			} else {
				$categoryDomain->setHidden(false);
			}
		}
	}

	/**
	 * @param int $categoryId
	 */
	public function deleteById($categoryId) {
		$category = $this->categoryRepository->getById($categoryId);
		$this->em->beginTransaction();
		$this->categoryService->setChildrenAsSiblings($category);
		// Normally, UnitOfWork performs UPDATEs on children after DELETE of main entity.
		// We need to update `parent` attribute of children first.
		$this->em->flush();

		$this->em->remove($category);
		$this->em->flush();
		$this->em->commit();
	}

	/**
	 * @param int[] $parentIdByCategoryId
	 */
	public function editOrdering($parentIdByCategoryId) {
		// eager-load all categories into identity map
		$this->categoryRepository->getAll();

		try {
			$this->em->beginTransaction();
			$rootCategory = $this->categoryRepository->getRootCategory();
			foreach ($parentIdByCategoryId as $categoryId => $parentId) {
				if ($parentId === null) {
					$parent = $rootCategory;
				} else {
					$parent = $this->categoryRepository->getById($parentId);
				}
				$category = $this->categoryRepository->getById($categoryId);
				$category->setParent($parent);
				$this->categoryRepository->moveDown($category, CategoryRepository::MOVE_DOWN_TO_BOTTOM);
			}

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAll() {
		return $this->categoryRepository->getAll();
	}

	/**
	 * @param string $locale
	 * @return \SS6\ShopBundle\Model\Category\Detail\CategoryDetail[]
	 */
	public function getAllCategoryDetails($locale) {
		$categories = $this->categoryRepository->getPreOrderTreeTraversalForAllCategories($locale);
		$categoryDetails = $this->categoryDetailFactory->createDetailsHierarchy($categories);

		return $categoryDetails;
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @return \SS6\ShopBundle\Model\Category\Detail\CategoryDetail[]
	 */
	public function getVisibleCategoryDetailsForDomain($domainId, $locale) {
		$categories = $this->categoryRepository->getPreOrderTreeTraversalForVisibleCategoriesByDomain($domainId, $locale);

		$categoryDetails = $this->categoryDetailFactory->createDetailsHierarchy($categories);

		return $categoryDetails;
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @param string $searchText
	 * @return \SS6\ShopBundle\Model\Category\Detail\CategoryDetail[]
	 */
	public function getVisibleByDomainAndSearchText($domainId, $locale, $searchText) {
		$categories = $this->categoryRepository->getVisibleByDomainIdAndSearchText(
			$domainId,
			$locale,
			$searchText
		);

		return $categories;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllWithoutBranch(Category $category) {
		return $this->categoryRepository->getAllWithoutBranch($category);
	}

	/**
	 * @param string|null $searchText
	 * @param int $limit
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getSearchAutocompleteCategories($searchText, $limit) {
		$page = 1;

		$paginationResult = $this->categoryRepository->getPaginationResultForSearchVisible(
			$searchText,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$page,
			$limit
		);

		return $paginationResult;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Category\Category[domainId]
	 */
	public function getProductMainCategoriesIndexedByDomainId(Product $product) {
		$mainCategoriesIndexedByDomainId = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$mainCategoriesIndexedByDomainId[$domainConfig->getId()] = $this->categoryRepository->findProductMainCategoryOnDomain(
				$product,
				$domainConfig->getId()
			);
		}

		return $mainCategoriesIndexedByDomainId;
	}

	/**
	 * @param Product $product
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function getProductMainCategoryByDomainId(Product $product, $domainId) {
		return $this->categoryRepository->getProductMainCategoryOnDomain($product, $domainId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return string[]
	 */
	public function getCategoryNamesInPathFromRootToProductMainCategoryOnDomain(Product $product, DomainConfig $domainConfig) {
		return $this->categoryRepository->getCategoryNamesInPathFromRootToProductMainCategoryOnDomain($product, $domainConfig);
	}
}

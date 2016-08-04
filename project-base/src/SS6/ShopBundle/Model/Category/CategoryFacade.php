<?php

namespace SS6\ShopBundle\Model\Category;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Image\ImageFacade;
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

	/**
	 * @var \SS6\ShopBundle\Component\Image\ImageFacade
	 */
	private $imageFacade;

	public function __construct(
		EntityManager $em,
		CategoryRepository $categoryRepository,
		CategoryService $categoryService,
		Domain $domain,
		CategoryVisibilityRecalculationScheduler $categoryVisibilityRecalculationScheduler,
		CategoryDetailFactory $categoryDetailFactory,
		FriendlyUrlFacade $friendlyUrlFacade,
		ImageFacade $imageFacade
	) {
		$this->em = $em;
		$this->categoryRepository = $categoryRepository;
		$this->categoryService = $categoryService;
		$this->domain = $domain;
		$this->categoryVisibilityRecalculationScheduler = $categoryVisibilityRecalculationScheduler;
		$this->categoryDetailFactory = $categoryDetailFactory;
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->imageFacade = $imageFacade;
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
		$rootCategory = $this->getRootCategory();
		$category = $this->categoryService->create($categoryData, $rootCategory);
		$this->em->persist($category);
		$this->em->flush($category);
		$this->createCategoryDomains($category, $categoryData, $this->domain->getAll());
		$this->friendlyUrlFacade->createFriendlyUrls('front_product_list', $category->getId(), $category->getNames());
		$this->imageFacade->uploadImage($category, $categoryData->image, null);

		$this->categoryVisibilityRecalculationScheduler->scheduleRecalculationWithoutDependencies();

		return $category;
	}

	/**
	 * @param int $categoryId
	 * @param \SS6\ShopBundle\Model\Category\CategoryData $categoryData
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function edit($categoryId, CategoryData $categoryData) {
		$rootCategory = $this->getRootCategory();
		$category = $this->categoryRepository->getById($categoryId);
		$this->categoryService->edit($category, $categoryData, $rootCategory);
		$this->refreshCategoryDomains($category, $categoryData);
		$this->friendlyUrlFacade->saveUrlListFormData('front_product_list', $category->getId(), $categoryData->urls);
		$this->friendlyUrlFacade->createFriendlyUrls('front_product_list', $category->getId(), $category->getNames());
		$this->imageFacade->uploadImage($category, $categoryData->image, null);
		$this->em->flush();

		$this->categoryVisibilityRecalculationScheduler->scheduleRecalculation($category);

		return $category;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param \SS6\ShopBundle\Model\Category\CategoryData $categoryData
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
	 */
	private function createCategoryDomains(Category $category, CategoryData $categoryData, array $domainConfigs) {
		$toFlush = [];

		foreach ($domainConfigs as $domainConfig) {
			$domainId = $domainConfig->getId();
			$categoryDomain = new CategoryDomain($category, $domainId);

			$categoryDomain->setDescription($categoryData->descriptions[$domainId]);

			$this->em->persist($categoryDomain);
			$toFlush[] = $categoryDomain;
		}

		$this->em->flush($toFlush);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param \SS6\ShopBundle\Model\Category\CategoryData $categoryData
	 */
	private function refreshCategoryDomains(Category $category, CategoryData $categoryData) {
		$categoryDomains = $this->categoryRepository->getCategoryDomainsByCategory($category);

		foreach ($categoryDomains as $categoryDomain) {
			$domainId = $categoryDomain->getDomainId();

			$categoryDomain->setDescription($categoryData->descriptions[$domainId]);
			if (in_array($domainId, $categoryData->hiddenOnDomains)) {
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
		$this->categoryService->setChildrenAsSiblings($category);
		// Normally, UnitOfWork performs UPDATEs on children after DELETE of main entity.
		// We need to update `parent` attribute of children first.
		$this->em->flush();

		$this->em->remove($category);
		$this->em->flush();
	}

	/**
	 * @param int[] $parentIdByCategoryId
	 */
	public function editOrdering($parentIdByCategoryId) {
		// eager-load all categories into identity map
		$this->categoryRepository->getAll();

		$rootCategory = $this->getRootCategory();
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
	 * @return \SS6\ShopBundle\Model\Category\Detail\CategoryDetail[]
	 */
	public function getVisibleFirstLevelCategoryDetailsForDomain($domainId, $locale) {
		$categories = $this->categoryRepository->getPreOrderTreeTraversalForVisibleFirstLevelCategoriesByDomain($domainId, $locale);

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
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllVisibleChildrenByCategoryAndDomainId(Category $category, $domainId) {
		return $this->categoryRepository->getAllVisibleChildrenByCategoryAndDomainId($category, $domainId);
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

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function getRootCategory() {
		return $this->categoryRepository->getRootCategory();
	}

	/**
	 * @param int $domainId
	 * @param int $categoryId
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function getVisibleOnDomainById($domainId, $categoryId) {
		$category = $this->getById($categoryId);
		$categoryDomain = $category->getCategoryDomain($domainId);
		if (!$categoryDomain->isVisible()) {
			$message = 'Category ID ' . $categoryId . ' is not visible on domain ID ' . $domainId;
			throw new \SS6\ShopBundle\Model\Category\Exception\CategoryNotFoundException($message);
		}

		return $category;
	}
}

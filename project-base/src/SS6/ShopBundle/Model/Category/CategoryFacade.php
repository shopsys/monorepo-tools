<?php

namespace SS6\ShopBundle\Model\Category;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Category\CategoryData;
use SS6\ShopBundle\Model\Category\CategoryService;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Domain\Domain;

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
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		EntityManager $em,
		CategoryRepository $categoryRepository,
		CategoryService $categoryService,
		Domain $domain
	) {
		$this->em = $em;
		$this->categoryRepository = $categoryRepository;
		$this->categoryService = $categoryService;
		$this->domain = $domain;
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
		$category = $this->categoryService->create($categoryData);
		$this->em->persist($category);
		$this->em->flush();

		return $category;
	}

	/**
	 * @param int $categoryId
	 * @param \SS6\ShopBundle\Model\Category\CategoryData $categoryData
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function edit($categoryId, CategoryData $categoryData) {
		$category = $this->categoryRepository->getById($categoryId);
		$this->categoryService->edit($category, $categoryData);
		$this->em->flush();

		return $category;
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
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllInRootWithTranslation() {
		$locale = $this->domain->getLocale();
		return $this->categoryRepository->getAllInRootWithTranslation($locale);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAll() {
		return $this->categoryRepository->getAll();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllWithoutBranch(Category $category) {
		return $this->categoryRepository->getAllWithoutBranch($category);
	}

}

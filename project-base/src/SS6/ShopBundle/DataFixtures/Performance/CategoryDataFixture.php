<?php

namespace SS6\ShopBundle\DataFixtures\Performance;

use Faker\Factory as FakerFactory;
use SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryData;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Category\CategoryRepository;

class CategoryDataFixture {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade
	 */
	private $sqlLoggerFacade;

	/**
	 * @var \Faker\Generator
	 */
	private $faker;

	/**
	 * @var int[]
	 */
	private $categoriesCountsByLevel;

	/**
	 * @var int
	 */
	private $categoriesCreated;

	public function __construct(
		CategoryRepository $categoryRepository,
		SqlLoggerFacade $sqlLoggerFacade,
		CategoryFacade $categoryFacade
	) {
		$this->categoryRepository = $categoryRepository;
		$this->categoryFacade = $categoryFacade;
		$this->sqlLoggerFacade = $sqlLoggerFacade;
		$this->faker = FakerFactory::create();
		$this->categoriesCountsByLevel = [2, 4, 6];
		$this->categoriesCreated = 0;
	}

	public function load() {
		$rootCategory = $this->categoryRepository->getRootCategory();
		$this->sqlLoggerFacade->temporarilyDisableLogging();
		$this->recursivelyCreateCategoryTree($rootCategory);
		$this->sqlLoggerFacade->reenableLogging();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $parentCategory
	 * @param int $categoryLevel
	 */
	private function recursivelyCreateCategoryTree($parentCategory, $categoryLevel = 0) {
		for ($i = 0; $i < $this->categoriesCountsByLevel[$categoryLevel]; $i++) {
			$categoryData = $this->getRandomCategoryDataByParentCategory($parentCategory);
			$newCategory = $this->categoryFacade->create($categoryData);
			$this->categoriesCreated++;
			if (array_key_exists($categoryLevel + 1, $this->categoriesCountsByLevel)) {
				$this->recursivelyCreateCategoryTree($newCategory, $categoryLevel + 1);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $parentCategory
	 * @return \SS6\ShopBundle\Model\Category\CategoryData
	 */
	private function getRandomCategoryDataByParentCategory(Category $parentCategory) {
		$categoryData = new CategoryData();
		$categoryName = $this->faker->word . ' #' . $this->categoriesCreated;
		$categoryData->name = ['cs' => $categoryName, 'en' => $categoryName];
		$categoryData->parent = $parentCategory;

		return $categoryData;
	}

}

<?php

namespace SS6\ShopBundle\DataFixtures\Performance;

use Faker\Generator as Faker;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryData;
use SS6\ShopBundle\Model\Category\CategoryFacade;

class CategoryDataFixture {

	const FIRST_PERFORMANCE_CATEGORY = 'first_performance_category';

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

	/**
	 * @var \SS6\ShopBundle\Component\DataFixture\PersistentReferenceFacade
	 */
	private $persistentReferenceFacade;

	public function __construct(
		CategoryFacade $categoryFacade,
		SqlLoggerFacade $sqlLoggerFacade,
		PersistentReferenceFacade $persistentReferenceFacade,
		Faker $faker
	) {
		$this->categoryFacade = $categoryFacade;
		$this->sqlLoggerFacade = $sqlLoggerFacade;
		$this->faker = $faker;
		$this->categoriesCountsByLevel = [2, 4, 6];
		$this->categoriesCreated = 0;
		$this->persistentReferenceFacade = $persistentReferenceFacade;
	}

	public function load() {
		$rootCategory = $this->categoryFacade->getRootCategory();
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
			if ($this->categoriesCreated === 1) {
				$this->persistentReferenceFacade->persistReference(self::FIRST_PERFORMANCE_CATEGORY, $newCategory);
			}
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
		$categoryData->descriptions = [
			1 => $this->faker->paragraph(3, false),
			2 => $this->faker->paragraph(3, false),
		];
		$categoryData->parent = $parentCategory;

		return $categoryData;
	}

}

<?php

namespace SS6\ShopBundle\DataFixtures\Performance;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryData;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Category\CategoryRepository;

class CategoryDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

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

	public function __construct() {
		$this->faker = FakerFactory::create();
		$this->categoriesCountsByLevel = [2, 4, 6];
		$this->categoriesCreated = 0;
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
	 */
	public function load(ObjectManager $objectManager) {
		$categoryRepository = $this->get(CategoryRepository::class);
		/* @var $categoryRepository \SS6\ShopBundle\Model\Category\CategoryRepository */
		$sqlLoggerFacade = $this->get(SqlLoggerFacade::class);
		/* @var $sqlLoggerFacade \SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade */
		$rootCategory = $categoryRepository->getRootCategory();

		$sqlLoggerFacade->temporarilyDisableLogging();
		$this->recursivelyCreateCategoryTree($rootCategory);
		$sqlLoggerFacade->reenableLogging();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $parentCategory
	 * @param int $categoryLevel
	 */
	private function recursivelyCreateCategoryTree($parentCategory, $categoryLevel = 0) {
		$categoryFacade = $this->get(CategoryFacade::class);
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */
		for ($i = 0; $i < $this->categoriesCountsByLevel[$categoryLevel]; $i++) {
			$categoryData = $this->getRandomCategoryDataByParentCategory($parentCategory);
			$newCategory = $categoryFacade->create($categoryData);
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

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [PricingGroupDataFixture::class];
	}

}

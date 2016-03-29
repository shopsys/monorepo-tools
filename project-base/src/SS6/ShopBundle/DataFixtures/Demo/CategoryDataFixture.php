<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\CategoryRootDataFixture;
use SS6\ShopBundle\Model\Category\CategoryData;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Category\CategoryVisibilityRepository;

class CategoryDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	const PREFIX = 'category_';

	const ELECTRONICS = 'electronics';
	const TV = 'tv';
	const PHOTO = 'photo';
	const PRINTERS = 'printers';
	const PC = 'pc';
	const PHONES = 'phones';
	const COFFEE = 'coffee';
	const BOOKS = 'books';
	const TOYS = 'toys';
	const GARDEN_TOOLS = 'garden_tools';
	const FOOD = 'food';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$categoryVisibilityRepository = $this->get(CategoryVisibilityRepository::class);
		/* @var $categoryVisibilityRepository \SS6\ShopBundle\Model\Category\CategoryVisibilityRepository */

		$categoryData = new CategoryData();

		$categoryData->name = ['cs' => 'Elektro', 'en' => 'Electronics'];
		$categoryData->parent = $this->getReference(CategoryRootDataFixture::ROOT);
		$electronicsCategory = $this->createCategory(self::ELECTRONICS, $categoryData);

		$categoryData->name = ['cs' => 'Televize, audio', 'en' => 'TV, audio'];
		$categoryData->parent = $electronicsCategory;
		$this->createCategory(self::TV, $categoryData);

		$categoryData->name = ['cs' => 'Fotoaparáty', 'en' => 'Cameras & Photo'];
		$this->createCategory(self::PHOTO, $categoryData);

		$categoryData->name = ['cs' => 'Tiskárny', 'en' => null];
		$this->createCategory(self::PRINTERS, $categoryData);

		$categoryData->name = ['cs' => 'Počítače & příslušenství', 'en' => null];
		$this->createCategory(self::PC, $categoryData);

		$categoryData->name = ['cs' => 'Mobilní telefony', 'en' => null];
		$this->createCategory(self::PHONES, $categoryData);

		$categoryData->name = ['cs' => 'Kávovary', 'en' => null];
		$this->createCategory(self::COFFEE, $categoryData);

		$categoryData->name = ['cs' => 'Knihy', 'en' => 'Books'];
		$categoryData->parent = $this->getReference(CategoryRootDataFixture::ROOT);
		$this->createCategory(self::BOOKS, $categoryData);

		$categoryData->name = ['cs' => 'Hračky a další', 'en' => null];
		$this->createCategory(self::TOYS, $categoryData);

		$categoryData->name = ['cs' => 'Zahradní náčiní', 'en' => 'Garden tools'];
		$this->createCategory(self::GARDEN_TOOLS, $categoryData);

		$categoryData->name = ['cs' => 'Jídlo', 'en' => 'Food'];
		$this->createCategory(self::FOOD, $categoryData);

		$categoryVisibilityRepository->refreshCategoriesVisibility();
	}

	/**
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Category\CategoryData $categoryData
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	private function createCategory($referenceName, CategoryData $categoryData) {
		$categoryFacade = $this->get(CategoryFacade::class);
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */

		$category = $categoryFacade->create($categoryData);
		$this->addReference(self::PREFIX . $referenceName, $category);

		return $category;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			CategoryRootDataFixture::class,
		];
	}

}

<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\CategoryRootDataFixture;
use SS6\ShopBundle\Model\Category\CategoryData;
use SS6\ShopBundle\Model\Category\CategoryFacade;

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

		$categoryData = new CategoryData();

		$categoryData->name = ['cs' => 'Elektro', 'en' => 'Electronics'];
		$categoryData->parent = $this->getReference(CategoryRootDataFixture::ROOT);
		$electronicsCategory = $this->createCategory($categoryData, self::ELECTRONICS);

		$categoryData->name = ['cs' => 'Televize, audio', 'en' => 'TV, audio'];
		$categoryData->parent = $electronicsCategory;
		$this->createCategory($categoryData, self::TV);

		$categoryData->name = ['cs' => 'Fotoaparáty', 'en' => 'Cameras & Photo'];
		$this->createCategory($categoryData, self::PHOTO);

		$categoryData->name = ['cs' => 'Tiskárny', 'en' => null];
		$this->createCategory($categoryData, self::PRINTERS);

		$categoryData->name = ['cs' => 'Počítače & příslušenství', 'en' => null];
		$this->createCategory($categoryData, self::PC);

		$categoryData->name = ['cs' => 'Mobilní telefony', 'en' => null];
		$this->createCategory($categoryData, self::PHONES);

		$categoryData->name = ['cs' => 'Kávovary', 'en' => null];
		$this->createCategory($categoryData, self::COFFEE);

		$categoryData->name = ['cs' => 'Knihy', 'en' => 'Books'];
		$categoryData->parent = $this->getReference(CategoryRootDataFixture::ROOT);
		$this->createCategory($categoryData, self::BOOKS);

		$categoryData->name = ['cs' => 'Hračky a další', 'en' => null];
		$this->createCategory($categoryData, self::TOYS);

		$categoryData->name = ['cs' => 'Zahradní náčiní', 'en' => 'Garden tools'];
		$this->createCategory($categoryData, self::GARDEN_TOOLS);

		$categoryData->name = ['cs' => 'Jídlo', 'en' => 'Food'];
		$this->createCategory($categoryData, self::FOOD);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\CategoryData $categoryData
	 * @param string|null $referenceName
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	private function createCategory(CategoryData $categoryData, $referenceName = null) {
		$categoryFacade = $this->get(CategoryFacade::class);
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */

		$category = $categoryFacade->create($categoryData);
		if ($referenceName !== null) {
			$this->addReference(self::PREFIX . $referenceName, $category);
		}

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

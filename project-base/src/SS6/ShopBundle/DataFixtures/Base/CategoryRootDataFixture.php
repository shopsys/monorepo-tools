<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryData;
use SS6\ShopBundle\Model\Category\CategoryDomain;
use SS6\ShopBundle\Model\Category\CategoryVisibilityRepository;
use SS6\ShopBundle\Model\Domain\Domain;

class CategoryRootDataFixture extends AbstractReferenceFixture {

	const ROOT = 'category_root';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$domain = $this->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$categoryVisibilityRepository = $this->get(CategoryVisibilityRepository::class);
		/* @var $categoryVisibilityRepository \SS6\ShopBundle\Model\Category\CategoryVisibilityRepository */

		$category = new Category(new CategoryData());
		$manager->persist($category);
		$manager->flush();
		$this->addReference(self::ROOT, $category);

		foreach ($domain->getAll() as $domainConfig) {
			$categoryDomain = new CategoryDomain($category, $domainConfig->getId());
			$manager->persist($categoryDomain);
		}

		$manager->flush();

		$categoryVisibilityRepository->refreshCategoriesVisibility();
	}

}

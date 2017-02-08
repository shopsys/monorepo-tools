<?php

namespace SS6\ShopBundle\Model\Category;

use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryData;

class CategoryDataFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	public function __construct(
		CategoryRepository $categoryRepository,
		FriendlyUrlFacade $friendlyUrlFacade
	) {
		$this->categoryRepository = $categoryRepository;
		$this->friendlyUrlFacade = $friendlyUrlFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Model\Category\CategoryData
	 */
	public function createFromCategory(Category $category) {
		$categoryDomains = $this->categoryRepository->getCategoryDomainsByCategory($category);

		$categoryData = new CategoryData();
		$categoryData->setFromEntity($category, $categoryDomains);

		foreach ($categoryDomains as $categoryDomain) {
			$domainId = $categoryDomain->getDomainId();

			$categoryData->urls->mainOnDomains[$domainId] =
				$this->friendlyUrlFacade->findMainFriendlyUrl($domainId, 'front_product_list', $category->getId());
		}

		return $categoryData;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\CategoryData
	 */
	public function createDefault() {
		$categoryData = new CategoryData();

		return $categoryData;
	}

}

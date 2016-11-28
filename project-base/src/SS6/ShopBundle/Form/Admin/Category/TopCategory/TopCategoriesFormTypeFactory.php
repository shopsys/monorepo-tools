<?php

namespace SS6\ShopBundle\Form\Admin\Category\TopCategory;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer;
use SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use SS6\ShopBundle\Form\Admin\Category\TopCategory\TopCategoriesFormType;
use SS6\ShopBundle\Model\Category\CategoryFacade;

class TopCategoriesFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
	 */
	private $removeDuplicatesTransformer;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer
	 */
	private $categoriesIdsToCategoriesTransformer;

	/**
	 * @param \SS6\ShopBundle\Model\Category\CategoryFacade $categoryFacade
	 * @param \SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
	 * @param \SS6\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer $categoriesIdsToCategoriesTransformer
	 */
	public function __construct(
		CategoryFacade $categoryFacade,
		RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer,
		CategoriesIdsToCategoriesTransformer $categoriesIdsToCategoriesTransformer
	) {
		$this->categoryFacade = $categoryFacade;
		$this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
		$this->categoriesIdsToCategoriesTransformer = $categoriesIdsToCategoriesTransformer;
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return \SS6\ShopBundle\Form\Admin\Category\TopCategory\TopCategoriesFormType
	 */
	public function create(DomainConfig $domainConfig) {
		$categoryNamesIndexedByIds = $this->categoryFacade->getNamesIndexedByIdsForDomain(
			$domainConfig->getId(),
			$domainConfig->getLocale()
		);

		return new TopCategoriesFormType(
			$categoryNamesIndexedByIds,
			$this->removeDuplicatesTransformer,
			$this->categoriesIdsToCategoriesTransformer
		);
	}

}

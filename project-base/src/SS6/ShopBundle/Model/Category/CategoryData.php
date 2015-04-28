<?php

namespace SS6\ShopBundle\Model\Category;

use SS6\ShopBundle\Component\Validator;
use SS6\ShopBundle\Model\Category\Category;

/**
 * @Validator\Auto(entity="SS6\ShopBundle\Model\Category\Category")
 */
class CategoryData {

	/**
	 * @var string[]
	 */
	public $name;

	/**
	 * @var \SS6\ShopBundle\Model\Category\Category|null
	 */
	public $parent;

	/**
	 * @var int[]
	 */
	public $hiddenOnDomains;

	/**
	 * @var array
	 */
	public $urls;

	/**
	 * @param string[] $name
	 * @param \SS6\ShopBundle\Model\Category\Category|null $parent
	 */
	public function __construct(
		array $name = [],
		Category $parent = null,
		array $hiddenOnDomains = []
	) {
		$this->name = $name;
		$this->parent = $parent;
		$this->hiddenOnDomains = $hiddenOnDomains;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 */
	public function setFromEntity(Category $category, array $categoryDomains) {
		$translations = $category->getTranslations();
		$names = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->name = $names;
		$this->parent = $category->getParent();
		$hiddenOnDomains = [];
		foreach ($categoryDomains as $categoryDomain) {
			if ($categoryDomain->isHidden()) {
				$hiddenOnDomains[] = $categoryDomain->getDomainId();
			}
		}
		$this->hiddenOnDomains = $hiddenOnDomains;
	}

}

<?php

namespace SS6\ShopBundle\Model\Category;

use SS6\ShopBundle\Form\UrlListData;
use SS6\ShopBundle\Model\Category\Category;

class CategoryData {

	/**
	 * @var string[]
	 */
	public $name;

	/**
	 * @var string[]
	 */
	public $descriptions;

	/**
	 * @var \SS6\ShopBundle\Model\Category\Category|null
	 */
	public $parent;

	/**
	 * @var int[]
	 */
	public $hiddenOnDomains;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\Category\FeedCategory|null
	 */
	public $heurekaCzFeedCategory;

	/**
	 * @var \SS6\ShopBundle\Form\UrlListData
	 */
	public $urls;

	/**
	 * @var string[]
	 */
	public $image;

	public function __construct() {
		$this->name = [];
		$this->descriptions = [];
		$this->hiddenOnDomains = [];
		$this->urls = new UrlListData();
		$this->image = [];
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param \SS6\ShopBundle\Model\Category\CategoryDomain[] $categoryDomains
	 */
	public function setFromEntity(Category $category, array $categoryDomains) {
		$translations = $category->getTranslations();
		$names = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->name = $names;
		$this->parent = $category->getParent();
		$this->heurekaCzFeedCategory = $category->getHeurekaCzFeedCategory();
		$descriptions = [];
		$hiddenOnDomains = [];
		foreach ($categoryDomains as $categoryDomain) {
			$descriptions[$categoryDomain->getDomainId()] = $categoryDomain->getDescription();
			if ($categoryDomain->isHidden()) {
				$hiddenOnDomains[] = $categoryDomain->getDomainId();
			}
		}
		$this->hiddenOnDomains = $hiddenOnDomains;
		$this->descriptions = $descriptions;
	}

}

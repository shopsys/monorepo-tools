<?php

namespace Shopsys\ShopBundle\Model\Category;

use Shopsys\ShopBundle\Form\UrlListData;
use Shopsys\ShopBundle\Model\Category\Category;

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
	 * @var \Shopsys\ShopBundle\Model\Category\Category|null
	 */
	public $parent;

	/**
	 * @var int[]
	 */
	public $hiddenOnDomains;

	/**
	 * @var \Shopsys\ShopBundle\Model\Feed\Category\FeedCategory|null
	 */
	public $heurekaCzFeedCategory;

	/**
	 * @var \Shopsys\ShopBundle\Form\UrlListData
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
	 * @param \Shopsys\ShopBundle\Model\Category\Category $category
	 * @param \Shopsys\ShopBundle\Model\Category\CategoryDomain[] $categoryDomains
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

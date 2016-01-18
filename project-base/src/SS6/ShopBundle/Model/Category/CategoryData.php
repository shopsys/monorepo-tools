<?php

namespace SS6\ShopBundle\Model\Category;

use SS6\ShopBundle\Component\Validator;
use SS6\ShopBundle\Form\UrlListType;
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
	 * @var \SS6\ShopBundle\Model\Feed\Category\FeedCategory|null
	 */
	public $heurekaCzFeedCategory;

	/**
	 * @var array
	 */
	public $urls;

	/**
	 * @var string[]
	 */
	public $image;

	public function __construct() {
		$this->name = [];
		$this->hiddenOnDomains = [];
		$this->urls = [
			UrlListType::TO_DELETE => [],
			UrlListType::MAIN_ON_DOMAINS => [],
			UrlListType::NEW_URLS => [],
		];
		$this->image = [];
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
		$this->heurekaCzFeedCategory = $category->getHeurekaCzFeedCategory();
		$hiddenOnDomains = [];
		foreach ($categoryDomains as $categoryDomain) {
			if ($categoryDomain->isHidden()) {
				$hiddenOnDomains[] = $categoryDomain->getDomainId();
			}
		}
		$this->hiddenOnDomains = $hiddenOnDomains;
	}

}

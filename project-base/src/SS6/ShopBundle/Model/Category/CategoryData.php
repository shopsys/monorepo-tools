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
	private $name;

	/**
	 * @var \SS6\ShopBundle\Model\Category\Category|null
	 */
	private $parent;

	/**
	 * @param string[] $name
	 * @param \SS6\ShopBundle\Model\Category\Category|null $parent
	 */
	public function __construct(array $name = [], Category $parent = null) {
		$this->name = $name;
		$this->parent = $parent;
	}

	/**
	 * @return string[]
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string[] $name
	 */
	public function setName(array $name) {
		$this->name = $name;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category\null $parent
	 */
	public function setParent(Category $parent = null) {
		$this->parent = $parent;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 */
	public function setFromEntity(Category $category) {
		$translations = $category->getTranslations();
		$names = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->setName($names);
		$this->setParent($category->getParent());
	}

}

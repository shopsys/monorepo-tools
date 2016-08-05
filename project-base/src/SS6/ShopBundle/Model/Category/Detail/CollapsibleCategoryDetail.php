<?php

namespace SS6\ShopBundle\Model\Category\Detail;

use SS6\ShopBundle\Model\Category\Category;

class CollapsibleCategoryDetail {

	/**
	 * @var \SS6\ShopBundle\Model\Category\Category
	 */
	private $category;

	/**
	 * @var bool
	 */
	private $hasChildren;

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param bool $hasChildren
	 */
	public function __construct(Category $category, $hasChildren) {
		$this->category = $category;
		$this->hasChildren = $hasChildren;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @return bool
	 */
	public function hasChildren() {
		return $this->hasChildren;
	}

}

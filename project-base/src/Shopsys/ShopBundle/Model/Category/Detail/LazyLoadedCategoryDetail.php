<?php

namespace SS6\ShopBundle\Model\Category\Detail;

use Closure;
use SS6\ShopBundle\Model\Category\Category;

class LazyLoadedCategoryDetail {

	/**
	 * @var \Closure
	 */
	private $lazyLoadChildrenCallback;

	/**
	 * @var \SS6\ShopBundle\Model\Category\Category
	 */
	private $category;

	/**
	 * @var bool
	 */
	private $hasChildren;

	/**
	 * @var \SS6\ShopBundle\Model\Category\Detail\LazyLoadedCategoryDetail[]|null
	 */
	private $children;

	/**
	 * @param \Closure $lazyLoadChildrenCallback
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param bool $hasChildren
	 */
	public function __construct(
		Closure $lazyLoadChildrenCallback,
		Category $category,
		$hasChildren
	) {
		$this->lazyLoadChildrenCallback = $lazyLoadChildrenCallback;
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

	/**
	 * @return \SS6\ShopBundle\Model\Category\Detail\LazyLoadedCategoryDetail[]
	 */
	public function getChildren() {
		if ($this->children === null) {
			$this->children = call_user_func($this->lazyLoadChildrenCallback);
		}

		return $this->children;
	}
}

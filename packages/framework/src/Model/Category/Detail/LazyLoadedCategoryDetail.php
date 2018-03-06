<?php

namespace Shopsys\FrameworkBundle\Model\Category\Detail;

use Closure;
use Shopsys\FrameworkBundle\Model\Category\Category;

class LazyLoadedCategoryDetail
{
    /**
     * @var \Closure
     */
    private $lazyLoadChildrenCallback;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\Category
     */
    private $category;

    /**
     * @var bool
     */
    private $hasChildren;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\Detail\LazyLoadedCategoryDetail[]|null
     */
    private $children;

    /**
     * @param \Closure $lazyLoadChildrenCallback
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
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
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->hasChildren;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Category\Detail\LazyLoadedCategoryDetail[]
     */
    public function getChildren()
    {
        if ($this->children === null) {
            $this->children = call_user_func($this->lazyLoadChildrenCallback);
        }

        return $this->children;
    }
}

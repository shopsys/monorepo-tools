<?php

namespace Shopsys\FrameworkBundle\Model\Category\Detail;

use Shopsys\FrameworkBundle\Model\Category\Category;

class CategoryDetail
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\Category
     */
    private $category;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\Detail\CategoryDetail[]
     */
    private $children;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param \Shopsys\FrameworkBundle\Model\Category\Detail\CategoryDetail[] $children
     */
    public function __construct(
        Category $category,
        array $children
    ) {
        $this->category = $category;
        $this->children = $children;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Category\Detail\CategoryDetail[]
     */
    public function getChildren()
    {
        return $this->children;
    }
}

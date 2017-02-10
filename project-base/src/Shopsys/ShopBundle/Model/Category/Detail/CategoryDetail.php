<?php

namespace Shopsys\ShopBundle\Model\Category\Detail;

use Shopsys\ShopBundle\Model\Category\Category;

class CategoryDetail
{
    /**
     * @var \Shopsys\ShopBundle\Model\Category\Category
     */
    private $category;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\Detail\CategoryDetail[]
     */
    private $children;

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param \Shopsys\ShopBundle\Model\Category\Detail\CategoryDetail[] $children
     */
    public function __construct(
        Category $category,
        array $children
    ) {
        $this->category = $category;
        $this->children = $children;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Category\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Category\Detail\CategoryDetail[]
     */
    public function getChildren()
    {
        return $this->children;
    }
}

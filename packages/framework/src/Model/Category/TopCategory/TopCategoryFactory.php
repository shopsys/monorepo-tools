<?php

namespace Shopsys\FrameworkBundle\Model\Category\TopCategory;

use Shopsys\FrameworkBundle\Model\Category\Category;

class TopCategoryFactory implements TopCategoryFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param int $domainId
     * @param int $position
     * @return \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategory
     */
    public function create(
        Category $category,
        int $domainId,
        int $position
    ): TopCategory {
        return new TopCategory($category, $domainId, $position);
    }
}

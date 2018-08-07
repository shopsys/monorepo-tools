<?php

namespace Shopsys\FrameworkBundle\Model\Category;

interface CategoryFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryData $data
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function create(CategoryData $data): Category;
}

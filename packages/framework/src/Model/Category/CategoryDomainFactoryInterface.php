<?php

namespace Shopsys\FrameworkBundle\Model\Category;

interface CategoryDomainFactoryInterface
{

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryDomain
     */
    public function create(Category $category, int $domainId): CategoryDomain;
}

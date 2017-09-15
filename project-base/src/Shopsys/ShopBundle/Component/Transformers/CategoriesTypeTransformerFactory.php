<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use Shopsys\ShopBundle\Model\Category\CategoryFacade;

class CategoriesTypeTransformerFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @param \Shopsys\ShopBundle\Model\Category\CategoryFacade $categoryFacade
     */
    public function __construct(CategoryFacade $categoryFacade)
    {
        $this->categoryFacade = $categoryFacade;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Component\Transformers\CategoriesTypeTransformer
     */
    public function create($domainId)
    {
        return new CategoriesTypeTransformer($this->categoryFacade, $domainId);
    }
}

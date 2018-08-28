<?php

namespace Shopsys\ShopBundle\Model\Category;

use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Symfony\Component\HttpFoundation\Request;

class CurrentCategoryResolver
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade
     */
    private $productFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     */
    public function __construct(
        CategoryFacade $categoryFacade,
        ProductFacade $productFacade
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->productFacade = $productFacade;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\Category|null
     */
    public function findCurrentCategoryByRequest(Request $request, $domainId)
    {
        $routeName = $request->get('_route');

        if ($routeName === 'front_product_list') {
            $categoryId = $request->get('id');
            $currentCategory = $this->categoryFacade->getById($categoryId);

            return $currentCategory;
        } elseif ($routeName === 'front_product_detail') {
            $productId = $request->get('id');
            $product = $this->productFacade->getById($productId);
            $currentCategory = $this->categoryFacade->getProductMainCategoryByDomainId($product, $domainId);

            return $currentCategory;
        }

        return null;
    }
}

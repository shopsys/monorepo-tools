<?php

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class ProductExtension extends \Twig_Extension
{

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    public function __construct(CategoryFacade $categoryFacade) {
        $this->categoryFacade = $categoryFacade;
    }

    /**
     * @return array
     */
    public function getFilters() {
        return [
            new Twig_SimpleFilter('productDisplayName', [$this, 'getProductDisplayName']),
            new Twig_SimpleFilter('productListDisplayName', [$this, 'getProductListDisplayName']),
        ];
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return [
            new Twig_SimpleFunction(
                'productMainCategory',
                [$this, 'getProductMainCategory']
            ),
            new Twig_SimpleFunction(
                'findProductMainCategory',
                [$this, 'findProductMainCategory']
            ),
        ];
    }

    /**
     * @return string
     */
    public function getName() {
        return 'product';
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return string
     */
    public function getProductDisplayName(Product $product) {
        if ($product->getName() === null) {
            return t('ID %productId%', [
                '%productId%' => $product->getId(),
            ]);
        }

        return $product->getName();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return string
     */
    public function getProductListDisplayName(Product $product) {
        if ($product->getName() === null) {
            return t('Product name in default language is not entered');
        }

        return $product->getName();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\Category
     */
    public function getProductMainCategory(Product $product, $domainId) {
        return $this->categoryFacade->getProductMainCategoryByDomainId($product, $domainId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\Category|null
     */
    public function findProductMainCategory(Product $product, $domainId) {
        return $this->categoryFacade->findProductMainCategoryByDomainId($product, $domainId);
    }

}

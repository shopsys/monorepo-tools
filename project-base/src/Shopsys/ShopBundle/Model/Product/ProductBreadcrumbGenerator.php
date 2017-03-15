<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbItem;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;

class ProductBreadcrumbGenerator implements BreadcrumbGeneratorInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        CategoryFacade $categoryFacade,
        Domain $domain
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFacade = $categoryFacade;
        $this->domain = $domain;
    }

    /**
     * {@inheritDoc}
     */
    public function getBreadcrumbItems($routeName, array $routeParameters = [])
    {
        $product = $this->productRepository->getById($routeParameters['id']);

        $productMainCategory = $this->categoryRepository->getProductMainCategoryOnDomain(
            $product,
            $this->domain->getId()
        );

        $breadcrumbItems = $this->getCategoryBreadcrumbItems($productMainCategory);

        $breadcrumbItems[] = new BreadcrumbItem(
            $product->getName()
        );

        return $breadcrumbItems;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @return \Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbItem[]
     */
    private function getCategoryBreadcrumbItems(Category $category)
    {
        $categoriesInPath = $this->categoryRepository->getVisibleCategoriesInPathFromRootOnDomain(
            $category,
            $this->domain->getId()
        );

        $breadcrumbItems = [];
        foreach ($categoriesInPath as $categoryInPath) {
            $breadcrumbItems[] = new BreadcrumbItem(
                $categoryInPath->getName(),
                'front_product_list',
                ['id' => $categoryInPath->getId()]
            );
        }

        return $breadcrumbItems;
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteNames()
    {
        return ['front_product_detail'];
    }
}

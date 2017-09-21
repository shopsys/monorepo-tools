<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbItem;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;

class ProductBreadcrumbGenerator implements BreadcrumbGeneratorInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

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
        CategoryFacade $categoryFacade,
        Domain $domain
    ) {
        $this->productRepository = $productRepository;
        $this->categoryFacade = $categoryFacade;
        $this->domain = $domain;
    }

    /**
     * {@inheritDoc}
     */
    public function getBreadcrumbItems($routeName, array $routeParameters = [])
    {
        $product = $this->productRepository->getById($routeParameters['id']);

        $productMainCategory = $this->categoryFacade->getProductMainCategoryByDomainId(
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
        $categoriesInPath = $this->categoryFacade->getVisibleCategoriesInPathFromRootOnDomain(
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

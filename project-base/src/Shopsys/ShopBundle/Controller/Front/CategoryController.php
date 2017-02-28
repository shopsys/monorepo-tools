<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Category\CurrentCategoryResolver;
use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryFacade;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends FrontBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Category\CurrentCategoryResolver
     */
    private $currentCategoryResolver;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryFacade
     */
    private $topCategoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    public function __construct(
        Domain $domain,
        CategoryFacade $categoryFacade,
        CurrentCategoryResolver $currentCategoryResolver,
        TopCategoryFacade $topCategoryFacade,
        CurrentCustomer $currentCustomer
    ) {
        $this->domain = $domain;
        $this->categoryFacade = $categoryFacade;
        $this->currentCategoryResolver = $currentCategoryResolver;
        $this->topCategoryFacade = $topCategoryFacade;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function panelAction(Request $request)
    {
        $categoryDetails = $this->categoryFacade->getVisibleLazyLoadedCategoryDetailsForParent(
            $this->categoryFacade->getRootCategory(),
            $this->domain->getCurrentDomainConfig()
        );
        $currentCategory = $this->currentCategoryResolver->findCurrentCategoryByRequest($request, $this->domain->getId());

        if ($currentCategory !== null) {
            $openCategories = $this->categoryFacade->getVisibleCategoriesInPathFromRootOnDomain(
                $currentCategory,
                $this->domain->getId()
            );
        } else {
            $openCategories = [];
        }

        return $this->render('@ShopsysShop/Front/Content/Category/panel.html.twig', [
            'lazyLoadedCategoryDetails' => $categoryDetails,
            'isFirstLevel' => true,
            'openCategories' => $openCategories,
            'currentCategory' => $currentCategory,
        ]);
    }

    /**
     * @param int $parentCategoryId
     */
    public function branchAction($parentCategoryId)
    {
        $parentCategory = $this->categoryFacade->getById($parentCategoryId);

        $categoryDetails = $this->categoryFacade->getVisibleLazyLoadedCategoryDetailsForParent(
            $parentCategory,
            $this->domain->getCurrentDomainConfig()
        );

        return $this->render('@ShopsysShop/Front/Content/Category/panel.html.twig', [
            'lazyLoadedCategoryDetails' => $categoryDetails,
            'isFirstLevel' => false,
            'openCategories' => [],
            'currentCategory' => null,
        ]);
    }

    public function topAction()
    {
        return $this->render('@ShopsysShop/Front/Content/Category/top.html.twig', [
            'categories' => $this->topCategoryFacade->getCategoriesForAll($this->domain->getId()),
        ]);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category[] $categories
     * @param bool $showProductsCountByCategory
     */
    public function categoryListAction(array $categories, $showProductsCountByCategory = true)
    {
        if ($showProductsCountByCategory === true) {
            $pricingGroup = $this->currentCustomer->getPricingGroup();
            $domainId = $this->domain->getId();

            $listableProductCountsIndexedByCategoryId = $this->categoryFacade
                ->getListableProductCountsIndexedByCategoryId($categories, $pricingGroup, $domainId);
        } else {
            $listableProductCountsIndexedByCategoryId = [];
        }

        return $this->render('@ShopsysShop/Front/Content/Category/categoryList.html.twig', [
            'categories' => $categories,
            'listableProductCountsIndexedByCategoryId' => $listableProductCountsIndexedByCategoryId,
        ]);
    }
}

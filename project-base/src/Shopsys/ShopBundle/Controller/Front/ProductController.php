<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\Front\Product\ProductFilterFormType;
use Shopsys\ShopBundle\Model\Advert\Advert;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Module\ModuleFacade;
use Shopsys\ShopBundle\Model\Module\ModuleList;
use Shopsys\ShopBundle\Model\Product\Brand\BrandFacade;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterConfigFactory;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingModeForBrandFacade;
use Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingModeForListFacade;
use Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingModeForSearchFacade;
use Shopsys\ShopBundle\Model\Product\ProductOnCurrentDomainFacade;
use Shopsys\ShopBundle\Twig\RequestExtension;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends FrontBaseController
{
    const SEARCH_TEXT_PARAMETER = 'q';
    const PAGE_QUERY_PARAMETER = 'page';
    const PRODUCTS_PER_PAGE = 12;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterConfigFactory
     */
    private $productFilterConfigFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductOnCurrentDomainFacade
     */
    private $productOnCurrentDomainFacade;

    /**
     * @var \Shopsys\ShopBundle\Twig\RequestExtension
     */
    private $requestExtension;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingModeForListFacade
     */
    private $productListOrderingModeForListFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingModeForBrandFacade
     */
    private $productListOrderingModeForBrandFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingModeForSearchFacade
     */
    private $productListOrderingModeForSearchFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Module\ModuleFacade
     */
    private $moduleFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandFacade
     */
    private $brandFacade;

    public function __construct(
        RequestExtension $requestExtension,
        CategoryFacade $categoryFacade,
        Domain $domain,
        ProductOnCurrentDomainFacade $productOnCurrentDomainFacade,
        ProductFilterConfigFactory $productFilterConfigFactory,
        ProductListOrderingModeForListFacade $productListOrderingModeForListFacade,
        ProductListOrderingModeForBrandFacade $productListOrderingModeForBrandFacade,
        ProductListOrderingModeForSearchFacade $productListOrderingModeForSearchFacade,
        ModuleFacade $moduleFacade,
        BrandFacade $brandFacade
    ) {
        $this->requestExtension = $requestExtension;
        $this->categoryFacade = $categoryFacade;
        $this->domain = $domain;
        $this->productOnCurrentDomainFacade = $productOnCurrentDomainFacade;
        $this->productFilterConfigFactory = $productFilterConfigFactory;
        $this->productListOrderingModeForListFacade = $productListOrderingModeForListFacade;
        $this->productListOrderingModeForBrandFacade = $productListOrderingModeForBrandFacade;
        $this->productListOrderingModeForSearchFacade = $productListOrderingModeForSearchFacade;
        $this->moduleFacade = $moduleFacade;
        $this->brandFacade = $brandFacade;
    }

    /**
     * @param int $id
     */
    public function detailAction($id)
    {
        $productDetail = $this->productOnCurrentDomainFacade->getVisibleProductDetailById($id);
        $product = $productDetail->getProduct();

        if ($product->isVariant()) {
            return $this->redirectToRoute('front_product_detail', ['id' => $product->getMainVariant()->getId()]);
        }

        $accessoriesDetails = $this->productOnCurrentDomainFacade->getAccessoriesProductDetailsForProduct($product);
        $variantsDetails = $this->productOnCurrentDomainFacade->getVariantsProductDetailsForProduct($product);
        $productMainCategory = $this->categoryFacade->getProductMainCategoryByDomainId($product, $this->domain->getId());

        return $this->render('@ShopsysShop/Front/Content/Product/detail.html.twig', [
            'productDetail' => $productDetail,
            'accesoriesDetails' => $accessoriesDetails,
            'variantsDetails' => $variantsDetails,
            'productMainCategory' => $productMainCategory,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function listByCategoryAction(Request $request, $id)
    {
        $category = $this->categoryFacade->getVisibleOnDomainById($this->domain->getId(), $id);

        $requestPage = $request->get(self::PAGE_QUERY_PARAMETER);
        if (!$this->isRequestPageValid($requestPage)) {
            return $this->redirectToRoute('front_product_list', $this->getRequestParametersWithoutPage());
        }
        $page = $requestPage === null ? 1 : (int)$requestPage;

        $orderingModeId = $this->productListOrderingModeForListFacade->getOrderingModeIdFromRequest(
            $request
        );

        $productFilterData = new ProductFilterData();

        $productFilterConfig = $this->createProductFilterConfigForCategory($category);
        $filterForm = $this->createForm(ProductFilterFormType::class, $productFilterData, [
            'product_filter_config' => $productFilterConfig,
        ]);
        $filterForm->handleRequest($request);

        $paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductDetailsInCategory(
            $productFilterData,
            $orderingModeId,
            $page,
            self::PRODUCTS_PER_PAGE,
            $id
        );

        $productFilterCountData = null;
        if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_FILTER_COUNTS)) {
            $productFilterCountData = $this->productOnCurrentDomainFacade->getProductFilterCountDataInCategory(
                $id,
                $productFilterConfig,
                $productFilterData
            );
        }

        $viewParameters = [
            'paginationResult' => $paginationResult,
            'productFilterCountData' => $productFilterCountData,
            'category' => $category,
            'categoryDomain' => $category->getCategoryDomain($this->domain->getId()),
            'filterForm' => $filterForm->createView(),
            'filterFormSubmited' => $filterForm->isSubmitted(),
            'visibleChildren' => $this->categoryFacade->getAllVisibleChildrenByCategoryAndDomainId($category, $this->domain->getId()),
            'priceRange' => $productFilterConfig->getPriceRange(),
        ];

        if ($request->isXmlHttpRequest()) {
            return $this->render('@ShopsysShop/Front/Content/Product/ajaxList.html.twig', $viewParameters);
        } else {
            $viewParameters['POSITION_PRODUCT_LIST'] = Advert::POSITION_PRODUCT_LIST;

            return $this->render('@ShopsysShop/Front/Content/Product/list.html.twig', $viewParameters);
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function listByBrandAction(Request $request, $id)
    {
        $requestPage = $request->get(self::PAGE_QUERY_PARAMETER);
        if (!$this->isRequestPageValid($requestPage)) {
            return $this->redirectToRoute('front_brand_detail', $this->getRequestParametersWithoutPage());
        }
        $page = $requestPage === null ? 1 : (int)$requestPage;

        $orderingModeId = $this->productListOrderingModeForBrandFacade->getOrderingModeIdFromRequest(
            $request
        );

        $paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductDetailsForBrand(
            $orderingModeId,
            $page,
            self::PRODUCTS_PER_PAGE,
            $id
        );

        $viewParameters = [
            'paginationResult' => $paginationResult,
            'brand' => $this->brandFacade->getById($id),
        ];

        if ($request->isXmlHttpRequest()) {
            return $this->render('@ShopsysShop/Front/Content/Product/ajaxListByBrand.html.twig', $viewParameters);
        } else {
            return $this->render('@ShopsysShop/Front/Content/Product/listByBrand.html.twig', $viewParameters);
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function searchAction(Request $request)
    {
        $searchText = $request->query->get(self::SEARCH_TEXT_PARAMETER);

        $requestPage = $request->get(self::PAGE_QUERY_PARAMETER);
        if (!$this->isRequestPageValid($requestPage)) {
            return $this->redirectToRoute('front_product_search', $this->getRequestParametersWithoutPage());
        }
        $page = $requestPage === null ? 1 : (int)$requestPage;

        $orderingModeId = $this->productListOrderingModeForSearchFacade->getOrderingModeIdFromRequest(
            $request
        );

        $productFilterData = new ProductFilterData();

        $productFilterConfig = $this->createProductFilterConfigForSearch($searchText);
        $filterForm = $this->createForm(ProductFilterFormType::class, $productFilterData, [
            'product_filter_config' => $productFilterConfig,
        ]);
        $filterForm->handleRequest($request);

        $paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductDetailsForSearch(
            $searchText,
            $productFilterData,
            $orderingModeId,
            $page,
            self::PRODUCTS_PER_PAGE
        );

        $productFilterCountData = null;
        if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_FILTER_COUNTS)) {
            $productFilterCountData = $this->productOnCurrentDomainFacade->getProductFilterCountDataForSearch(
                $searchText,
                $productFilterConfig,
                $productFilterData
            );
        }

        $viewParameters = [
            'paginationResult' => $paginationResult,
            'productFilterCountData' => $productFilterCountData,
            'filterForm' => $filterForm->createView(),
            'filterFormSubmited' => $filterForm->isSubmitted(),
            'searchText' => $searchText,
            'SEARCH_TEXT_PARAMETER' => self::SEARCH_TEXT_PARAMETER,
            'priceRange' => $productFilterConfig->getPriceRange(),
        ];

        if ($request->isXmlHttpRequest()) {
            return $this->render('@ShopsysShop/Front/Content/Product/ajaxSearch.html.twig', $viewParameters);
        } else {
            $viewParameters['foundCategories'] = $this->searchCategories($searchText);
            return $this->render('@ShopsysShop/Front/Content/Product/search.html.twig', $viewParameters);
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @return \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterConfig
     */
    private function createProductFilterConfigForCategory(Category $category)
    {
        return $this->productFilterConfigFactory->createForCategory(
            $this->domain->getId(),
            $this->domain->getLocale(),
            $category
        );
    }

    /**
     * @param string|null $searchText
     * @return \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterConfig
     */
    private function createProductFilterConfigForSearch($searchText)
    {
        return $this->productFilterConfigFactory->createForSearch(
            $this->domain->getId(),
            $this->domain->getLocale(),
            $searchText
        );
    }

    /**
     * @param string|null $searchText
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    private function searchCategories($searchText)
    {
        return $this->categoryFacade->getVisibleByDomainAndSearchText(
            $this->domain->getId(),
            $this->domain->getLocale(),
            $searchText
        );
    }

    public function selectOrderingModeForListAction(Request $request)
    {
        $productListOrderingConfig = $this->productListOrderingModeForListFacade->getProductListOrderingConfig();

        $orderingModeId = $this->productListOrderingModeForListFacade->getOrderingModeIdFromRequest(
            $request
        );

        return $this->render('@ShopsysShop/Front/Content/Product/orderingSetting.html.twig', [
            'orderingModesNames' => $productListOrderingConfig->getSupportedOrderingModesNamesIndexedById(),
            'activeOrderingModeId' => $orderingModeId,
            'cookieName' => $productListOrderingConfig->getCookieName(),
        ]);
    }

    public function selectOrderingModeForListByBrandAction(Request $request)
    {
        $productListOrderingConfig = $this->productListOrderingModeForBrandFacade->getProductListOrderingConfig();

        $orderingModeId = $this->productListOrderingModeForBrandFacade->getOrderingModeIdFromRequest(
            $request
        );

        return $this->render('@ShopsysShop/Front/Content/Product/orderingSetting.html.twig', [
            'orderingModesNames' => $productListOrderingConfig->getSupportedOrderingModesNamesIndexedById(),
            'activeOrderingModeId' => $orderingModeId,
            'cookieName' => $productListOrderingConfig->getCookieName(),
        ]);
    }

    public function selectOrderingModeForSearchAction(Request $request)
    {
        $productListOrderingConfig = $this->productListOrderingModeForSearchFacade->getProductListOrderingConfig();

        $orderingModeId = $this->productListOrderingModeForSearchFacade->getOrderingModeIdFromRequest(
            $request
        );

        return $this->render('@ShopsysShop/Front/Content/Product/orderingSetting.html.twig', [
            'orderingModesNames' => $productListOrderingConfig->getSupportedOrderingModesNamesIndexedById(),
            'activeOrderingModeId' => $orderingModeId,
            'cookieName' => $productListOrderingConfig->getCookieName(),
        ]);
    }

    /**
     * @param string|null $page
     * @return bool
     */
    private function isRequestPageValid($page)
    {
        return $page === null || (preg_match('@^([2-9]|[1-9][0-9]+)$@', $page));
    }

    /**
     * @return array
     */
    private function getRequestParametersWithoutPage()
    {
        $parameters = $this->requestExtension->getAllRequestParams();
        unset($parameters[self::PAGE_QUERY_PARAMETER]);
        return $parameters;
    }
}

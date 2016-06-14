<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Form\Front\Product\ProductFilterFormTypeFactory;
use SS6\ShopBundle\Model\Advert\AdvertPositionList;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Module\ModuleFacade;
use SS6\ShopBundle\Model\Module\ModuleList;
use SS6\ShopBundle\Model\Product\Brand\BrandFacade;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeForBrandFacade;
use SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeForListFacade;
use SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeForSearchFacade;
use SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade;
use SS6\ShopBundle\Twig\RequestExtension;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends FrontBaseController {

	const SEARCH_TEXT_PARAMETER = 'q';
	const PAGE_QUERY_PARAMETER = 'page';
	const PRODUCTS_PER_PAGE = 12;

	/**
	 * @var \SS6\ShopBundle\Form\Front\Product\ProductFilterFormTypeFactory
	 */
	private $productFilterFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade
	 */
	private $productOnCurrentDomainFacade;

	/**
	 * @var \SS6\ShopBundle\Twig\RequestExtension
	 */
	private $requestExtension;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeForListFacade
	 */
	private $productListOrderingModeForListFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeForBrandFacade
	 */
	private $productListOrderingModeForBrandFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeForSearchFacade
	 */
	private $productListOrderingModeForSearchFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Module\ModuleFacade
	 */
	private $moduleFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\BrandFacade
	 */
	private $brandFacade;

	public function __construct(
		RequestExtension $requestExtension,
		CategoryFacade $categoryFacade,
		Domain $domain,
		ProductOnCurrentDomainFacade $productOnCurrentDomainFacade,
		ProductFilterFormTypeFactory $productFilterFormTypeFactory,
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
		$this->productFilterFormTypeFactory = $productFilterFormTypeFactory;
		$this->productListOrderingModeForListFacade = $productListOrderingModeForListFacade;
		$this->productListOrderingModeForBrandFacade = $productListOrderingModeForBrandFacade;
		$this->productListOrderingModeForSearchFacade = $productListOrderingModeForSearchFacade;
		$this->moduleFacade = $moduleFacade;
		$this->brandFacade = $brandFacade;
	}

	/**
	 * @param int $id
	 */
	public function detailAction($id) {
		$productDetail = $this->productOnCurrentDomainFacade->getVisibleProductDetailById($id);
		$product = $productDetail->getProduct();

		if ($product->isVariant()) {
			return $this->redirectToRoute('front_product_detail', ['id' => $product->getMainVariant()->getId()]);
		}

		$accessoriesDetails = $this->productOnCurrentDomainFacade->getAccessoriesProductDetailsForProduct($product);
		$variantsDetails = $this->productOnCurrentDomainFacade->getVariantsProductDetailsForProduct($product);
		$productMainCategory = $this->categoryFacade->getProductMainCategoryByDomainId($product, $this->domain->getId());

		return $this->render('@SS6Shop/Front/Content/Product/detail.html.twig', [
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
	public function listByCategoryAction(Request $request, $id) {
		$category = $this->categoryFacade->getVisibleOnDomainById($this->domain->getId(), $id);

		$requestPage = $request->get(self::PAGE_QUERY_PARAMETER);
		if (!$this->isRequestPageValid($requestPage)) {
			return $this->redirectToRoute('front_product_list', $this->getRequestParametersWithoutPage());
		}
		$page = $requestPage === null ? 1 : (int)$requestPage;

		$orderingMode = $this->productListOrderingModeForListFacade->getOrderingModeFromRequest(
			$request
		);

		$productFilterData = new ProductFilterData();

		$productFilterFormType = $this->createProductFilterFormTypeForCategory($category);
		$filterForm = $this->createForm($productFilterFormType);
		$filterForm->setData($productFilterData);
		$filterForm->handleRequest($request);

		$paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductDetailsInCategory(
			$productFilterData,
			$orderingMode,
			$page,
			self::PRODUCTS_PER_PAGE,
			$id
		);

		$productFilterCountData = null;
		if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_FILTER_COUNTS)) {
			$productFilterCountData = $this->productOnCurrentDomainFacade->getProductFilterCountDataInCategory(
				$id,
				$productFilterFormType->getBrandFilterChoices(),
				$productFilterFormType->getFlagFilterChoices(),
				$productFilterFormType->getParameterFilterChoices(),
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
		];

		if ($request->isXmlHttpRequest()) {
			return $this->render('@SS6Shop/Front/Content/Product/ajaxList.html.twig', $viewParameters);
		} else {
			$viewParameters['POSITION_PRODUCT_LIST'] = AdvertPositionList::POSITION_PRODUCT_LIST;

			return $this->render('@SS6Shop/Front/Content/Product/list.html.twig', $viewParameters);
		}
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $brandId
	 */
	public function listByBrandAction(Request $request, $brandId) {
		$requestPage = $request->get(self::PAGE_QUERY_PARAMETER);
		if (!$this->isRequestPageValid($requestPage)) {
			return $this->redirectToRoute('front_brand_detail', $this->getRequestParametersWithoutPage());
		}
		$page = $requestPage === null ? 1 : (int)$requestPage;

		$orderingMode = $this->productListOrderingModeForBrandFacade->getOrderingModeFromRequest(
			$request
		);

		$paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductDetailsForBrand(
			$orderingMode,
			$page,
			self::PRODUCTS_PER_PAGE,
			$brandId
		);

		$viewParameters = [
			'paginationResult' => $paginationResult,
			'brand' => $this->brandFacade->getById($brandId),
		];

		return $this->render('@SS6Shop/Front/Content/Product/listByBrand.html.twig', $viewParameters);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function searchAction(Request $request) {
		$searchText = $request->query->get(self::SEARCH_TEXT_PARAMETER);

		$requestPage = $request->get(self::PAGE_QUERY_PARAMETER);
		if (!$this->isRequestPageValid($requestPage)) {
			$parameters = $request->query->all();
			unset($parameters[self::PAGE_QUERY_PARAMETER]);
			return $this->redirectToRoute('front_product_search', $parameters);
		}
		$page = $requestPage === null ? 1 : (int)$requestPage;

		$orderingMode = $this->productListOrderingModeForSearchFacade->getOrderingModeFromRequest(
			$request
		);

		$productFilterData = new ProductFilterData();

		$productFilterFormType = $this->createProductFilterFormTypeForSearch($searchText);
		$filterForm = $this->createForm($productFilterFormType);
		$filterForm->setData($productFilterData);
		$filterForm->handleRequest($request);

		$paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductDetailsForSearch(
			$searchText,
			$productFilterData,
			$orderingMode,
			$page,
			self::PRODUCTS_PER_PAGE
		);

		$productFilterCountData = null;
		if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_FILTER_COUNTS)) {
			$productFilterCountData = $this->productOnCurrentDomainFacade->getProductFilterCountDataForSearch(
				$searchText,
				$productFilterFormType->getBrandFilterChoices(),
				$productFilterFormType->getFlagFilterChoices(),
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
		];

		if ($request->isXmlHttpRequest()) {
			return $this->render('@SS6Shop/Front/Content/Product/ajaxSearch.html.twig', $viewParameters);
		} else {
			$viewParameters['foundCategories'] = $this->searchCategories($searchText);
			return $this->render('@SS6Shop/Front/Content/Product/search.html.twig', $viewParameters);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Form\Front\Product\ProductFilterFormType
	 */
	private function createProductFilterFormTypeForCategory(Category $category) {
		return $this->productFilterFormTypeFactory->createForCategory(
			$this->domain->getId(),
			$this->domain->getLocale(),
			$category
		);
	}

	/**
	 * @param string|null $searchText
	 * @return \SS6\ShopBundle\Form\Front\Product\ProductFilterFormType
	 */
	private function createProductFilterFormTypeForSearch($searchText) {
		return $this->productFilterFormTypeFactory->createForSearch(
			$this->domain->getId(),
			$this->domain->getLocale(),
			$searchText
		);
	}

	/**
	 * @param string|null $searchText
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	private function searchCategories($searchText) {
		return $this->categoryFacade->getVisibleByDomainAndSearchText(
			$this->domain->getId(),
			$this->domain->getLocale(),
			$searchText
		);
	}

	public function selectOrderingModeForListAction(Request $request) {
		$productListOrderingConfig = $this->productListOrderingModeForListFacade->getProductListOrderingConfig();

		$orderingMode = $this->productListOrderingModeForListFacade->getOrderingModeFromRequest(
			$request
		);

		return $this->render('@SS6Shop/Front/Content/Product/orderingSetting.html.twig', [
			'orderingModesNames' => $productListOrderingConfig->getSupportedOrderingModesNames(),
			'activeOrderingMode' => $orderingMode,
			'cookieName' => $productListOrderingConfig->getCookieName(),
		]);
	}

	public function selectOrderingModeForListByBrandAction(Request $request) {
		$productListOrderingConfig = $this->productListOrderingModeForBrandFacade->getProductListOrderingConfig();

		$orderingMode = $this->productListOrderingModeForBrandFacade->getOrderingModeFromRequest(
			$request
		);

		return $this->render('@SS6Shop/Front/Content/Product/orderingSetting.html.twig', [
			'orderingModesNames' => $productListOrderingConfig->getSupportedOrderingModesNames(),
			'activeOrderingMode' => $orderingMode,
			'cookieName' => $productListOrderingConfig->getCookieName(),
		]);
	}

	public function selectOrderingModeForSearchAction(Request $request) {
		$productListOrderingConfig = $this->productListOrderingModeForSearchFacade->getProductListOrderingConfig();

		$orderingMode = $this->productListOrderingModeForSearchFacade->getOrderingModeFromRequest(
			$request
		);

		return $this->render('@SS6Shop/Front/Content/Product/orderingSetting.html.twig', [
			'orderingModesNames' => $productListOrderingConfig->getSupportedOrderingModesNames(),
			'activeOrderingMode' => $orderingMode,
			'cookieName' => $productListOrderingConfig->getCookieName(),
		]);
	}

	/**
	 * @param string|null $page
	 * @return bool
	 */
	private function isRequestPageValid($page) {
		return $page === null || (preg_match('@^([2-9]|[1-9][0-9]+)$@', $page));
	}

	/**
	 * @return array
	 */
	private function getRequestParametersWithoutPage() {
		$parameters = $this->requestExtension->getAllRequestParams();
		unset($parameters[self::PAGE_QUERY_PARAMETER]);
		return $parameters;
	}

}

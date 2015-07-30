<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Product\OrderingSettingFormType;
use SS6\ShopBundle\Form\Front\Product\ProductFilterFormTypeFactory;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\ProductListOrderingService;
use SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade;
use SS6\ShopBundle\Twig\RequestExtension;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller {

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
	 * @var \SS6\ShopBundle\Model\Domain\Domain
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
	 * @var \SS6\ShopBundle\Model\Product\ProductListOrderingService
	 */
	private $productListOrderingService;

	public function __construct(
		RequestExtension $requestExtension,
		CategoryFacade $categoryFacade,
		Domain $domain,
		ProductOnCurrentDomainFacade $productOnCurrentDomainFacade,
		ProductFilterFormTypeFactory $productFilterFormTypeFactory,
		ProductListOrderingService $productListOrderingService
	) {
		$this->requestExtension = $requestExtension;
		$this->categoryFacade = $categoryFacade;
		$this->domain = $domain;
		$this->productOnCurrentDomainFacade = $productOnCurrentDomainFacade;
		$this->productFilterFormTypeFactory = $productFilterFormTypeFactory;
		$this->productListOrderingService = $productListOrderingService;
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
		$category = $this->categoryFacade->getById($id);

		$requestPage = $request->get(self::PAGE_QUERY_PARAMETER);
		if (!$this->isRequestPageValid($requestPage)) {
			$parameters = $this->requestExtension->getAllRequestParams();
			unset($parameters[self::PAGE_QUERY_PARAMETER]);
			return $this->redirect($this->generateUrl('front_product_list', $parameters));
		}
		$page = $requestPage === null ? 1 : (int)$requestPage;

		$orderingSetting = $this->productListOrderingService->getOrderingSettingFromRequest($request);

		$productFilterData = new ProductFilterData();

		$filterForm = $this->createFilterFormForCategory($category);
		$filterForm->setData($productFilterData);
		$filterForm->handleRequest($request);

		$paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductDetailsInCategory(
			$productFilterData,
			$orderingSetting,
			$page,
			self::PRODUCTS_PER_PAGE,
			$id
		);

		$productFilterCountData = $this->productOnCurrentDomainFacade->getProductFilterCountDataInCategory(
			$id,
			$productFilterData
		);

		$viewParameters = [
			'paginationResult' => $paginationResult,
			'productFilterCountData' => $productFilterCountData,
			'category' => $category,
			'filterForm' => $filterForm->createView(),
			'filterFormSubmited' => $filterForm->isSubmitted(),
		];

		if ($request->isXmlHttpRequest()) {
			return $this->render('@SS6Shop/Front/Content/Product/ajaxList.html.twig', $viewParameters);
		} else {
			return $this->render('@SS6Shop/Front/Content/Product/list.html.twig', $viewParameters);
		}
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
			return $this->redirect($this->generateUrl('front_product_search', $parameters));
		}
		$page = $requestPage === null ? 1 : (int)$requestPage;

		$orderingSetting = $this->productListOrderingService->getOrderingSettingFromRequest($request);

		$productFilterData = new ProductFilterData();

		$filterForm = $this->createFilterFormForSearch($searchText);
		$filterForm->setData($productFilterData);
		$filterForm->handleRequest($request);

		$paginationResult = $this->productOnCurrentDomainFacade->getPaginatedProductDetailsForSearch(
			$searchText,
			$productFilterData,
			$orderingSetting,
			$page,
			self::PRODUCTS_PER_PAGE
		);

		$productFilterCountData = $this->productOnCurrentDomainFacade->getProductFilterCountDataForSearch(
			$searchText,
			$productFilterData
		);

		$viewParameters = [
			'paginationResult' => $paginationResult,
			'productFilterCountData' => $productFilterCountData,
			'filterForm' => $filterForm->createView(),
			'filterFormSubmited' => $filterForm->isSubmitted(),
			'searchText' => $searchText,
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
	 * @return \Symfony\Component\Form\Form
	 */
	private function createFilterFormForCategory(Category $category) {
		return $this->createForm($this->productFilterFormTypeFactory->createForCategory(
			$this->domain->getId(),
			$this->domain->getLocale(),
			$category
		));
	}

	/**
	 * @param string|null $searchText
	 * @return \Symfony\Component\Form\Form
	 */
	private function createFilterFormForSearch($searchText) {
		return $this->createForm($this->productFilterFormTypeFactory->createForSearch(
			$this->domain->getId(),
			$this->domain->getLocale(),
			$searchText
		));
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

	public function selectOrderingModeAction(Request $request) {
		$orderingSetting = $this->productListOrderingService->getOrderingSettingFromRequest($request);
		$form = $this->createForm(new OrderingSettingFormType());
		$form->setData(['orderingMode' => $orderingSetting->getOrderingMode()]);

		return $this->render('@SS6Shop/Front/Content/Product/orderingSetting.html.twig', [
			'form' => $form->createView(),
			'cookieName' => ProductListOrderingService::COOKIE_NAME,
		]);
	}

	/**
	 * @param string|null $page
	 * @return bool
	 */
	private function isRequestPageValid($page) {
		return $page === null || (preg_match('@^([2-9]|[1-9][0-9]+)$@', $page));
	}

}

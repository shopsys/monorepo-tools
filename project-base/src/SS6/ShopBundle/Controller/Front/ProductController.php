<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Product\OrderingSettingFormType;
use SS6\ShopBundle\Form\Front\Product\ProductFilterFormTypeFactory;
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

		$accessoriesDetails = $this->productOnCurrentDomainFacade
			->getAccessoriesProductDetailsForProduct($productDetail->getProduct());
		$variantsDetails = $this->productOnCurrentDomainFacade
			->getVariantsProductDetailsForProduct($productDetail->getProduct());

		return $this->render('@SS6Shop/Front/Content/Product/detail.html.twig', [
			'productDetail' => $productDetail,
			'accesoriesDetails' => $accessoriesDetails,
			'variantsDetails' => $variantsDetails,
			'productMainCategory' => $this->categoryFacade->getProductMainCategoryByDomainId(
				$productDetail->getProduct(),
				$this->domain->getId()
			),
		]);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function listByCategoryAction(Request $request, $id) {
		$category = $this->categoryFacade->getById($id);

		$page = $request->get(self::PAGE_QUERY_PARAMETER);
		if ($page === '1') {
			$params = $this->requestExtension->getAllRequestParams();
			unset($params['page']);
			return $this->redirect($this->generateUrl(
				'front_product_list',
				$params
			));
		}

		$orderingSetting = $this->productListOrderingService->getOrderingSettingFromRequest($request);

		$productFilterData = new ProductFilterData();

		$filterForm = $this->createForm($this->productFilterFormTypeFactory->createForCategory(
			$this->domain->getId(),
			$this->domain->getLocale(),
			$category
		));
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

		if ($request->isXmlHttpRequest()) {
			return $this->render('@SS6Shop/Front/Content/Product/ajaxList.html.twig', [
				'paginationResult' => $paginationResult,
				'productFilterCountData' => $productFilterCountData,
				'category' => $category,
				'filterForm' => $filterForm->createView(),
				'filterFormSubmited' => $filterForm->isSubmitted(),
			]);
		}

		return $this->render('@SS6Shop/Front/Content/Product/list.html.twig', [
			'productDetails' => $paginationResult->getResults(),
			'orderingSetting' => $orderingSetting,
			'paginationResult' => $paginationResult,
			'productFilterCountData' => $productFilterCountData,
			'category' => $category,
			'filterForm' => $filterForm->createView(),
			'filterFormSubmited' => $filterForm->isSubmitted(),
			'domainId' => $this->domain->getId(),
		]);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function searchAction(Request $request) {
		$searchText = $request->query->get(self::SEARCH_TEXT_PARAMETER);

		$page = $request->get(self::PAGE_QUERY_PARAMETER);

		if ($page === '1') {
			return $this->redirect($this->generateUrl('front_product_search', $request->query->all()));
		}

		$orderingSetting = $this->productListOrderingService->getOrderingSettingFromRequest($request);

		$productFilterData = new ProductFilterData();

		$filterForm = $this->createForm($this->productFilterFormTypeFactory->createForSearch(
			$this->domain->getId(),
			$this->domain->getLocale(),
			$searchText
		));
		$filterForm->setData($productFilterData);
		$filterForm->handleRequest($request);

		$foundCategories = $this->categoryFacade->getVisibleByDomainAndSearchText(
			$this->domain->getId(),
			$this->domain->getLocale(),
			$searchText
		);

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

		if ($request->isXmlHttpRequest()) {
			return $this->render('@SS6Shop/Front/Content/Product/ajaxSearch.html.twig', [
				'paginationResult' => $paginationResult,
				'productFilterCountData' => $productFilterCountData,
				'filterForm' => $filterForm->createView(),
				'filterFormSubmited' => $filterForm->isSubmitted(),
				'searchText' => $searchText,
			]);
		}

		return $this->render('@SS6Shop/Front/Content/Product/search.html.twig', [
			'searchText' => $searchText,
			'foundCategories' => $foundCategories,
			'productDetails' => $paginationResult->getResults(),
			'orderingSetting' => $orderingSetting,
			'paginationResult' => $paginationResult,
			'productFilterCountData' => $productFilterCountData,
			'filterForm' => $filterForm->createView(),
			'filterFormSubmited' => $filterForm->isSubmitted(),
			'domainId' => $this->domain->getId(),
		]);
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

}

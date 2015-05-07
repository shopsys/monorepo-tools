<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Product\OrderingSettingFormType;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\ProductListOrderingService;
use SS6\ShopBundle\Twig\RequestExtension;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller {

	const PAGE_QUERY_PARAMETER = 'page';
	const PRODUCTS_PER_PAGE = 12;

	/**
	 * @var \SS6\ShopBundle\Twig\RequestExtension
	 */
	private $requestExtension;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		RequestExtension $requestExtension,
		CategoryFacade $categoryFacade,
		Domain $domain
	) {
		$this->requestExtension = $requestExtension;
		$this->categoryFacade = $categoryFacade;
		$this->domain = $domain;
	}

	/**
	 * @param int $id
	 */
	public function detailAction($id) {
		$productOnCurrentDomainFacade = $this->get('ss6.shop.product.product_on_current_domain_facade');
		/* @var $productOnCurrentDomainFacade \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade */

		$productDetail = $productOnCurrentDomainFacade->getVisibleProductDetailById($id);

		$accessoriesDetails = $productOnCurrentDomainFacade
			->getAccessoriesProductDetailsForProduct($productDetail->getProduct());

		return $this->render('@SS6Shop/Front/Content/Product/detail.html.twig', [
			'productDetail' => $productDetail,
			'accesoriesDetails' => $accessoriesDetails,
			'productMainCategory' => $this->categoryFacade->getProductMainCategoryByDomainId(
				$productDetail->getProduct(),
				$this->domain->getId()
			),
		]);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function listByCategoryAction(Request $request, $id) {
		$productOnCurrentDomainFacade = $this->get('ss6.shop.product.product_on_current_domain_facade');
		/* @var $productOnCurrentDomainFacade \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade */
		$productListOrderingService = $this->get('ss6.shop.product.product_list_ordering_service');
		/* @var $productListOrderingService \SS6\ShopBundle\Model\Product\ProductListOrderingService */
		$categoryFacade = $this->get('ss6.shop.category.category_facade');
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$productFilterFormTypeFactory = $this->get('ss6.shop.form.front.product.product_filter_form_type_factory');
		/* @var $productFilterFormTypeFactory \SS6\ShopBundle\Form\Front\Product\ProductFilterFormTypeFactory */

		$category = $categoryFacade->getById($id);

		$page = $request->get(self::PAGE_QUERY_PARAMETER);
		if ($page === '1') {
			$params = $this->requestExtension->getAllRequestParams();
			unset($params['page']);
			return $this->redirect($this->generateUrl(
				'front_product_list',
				$params
			));
		}

		$orderingSetting = $productListOrderingService->getOrderingSettingFromRequest($request);

		$productFilterData = new ProductFilterData();

		$filterForm = $this->createForm($productFilterFormTypeFactory->createForCategory(
			$domain->getId(),
			$domain->getLocale(),
			$category
		));
		$filterForm->setData($productFilterData);
		$filterForm->handleRequest($request);

		$paginationResult = $productOnCurrentDomainFacade->getPaginatedProductDetailsInCategory(
			$productFilterData,
			$orderingSetting,
			$page,
			self::PRODUCTS_PER_PAGE,
			$id
		);

		return $this->render('@SS6Shop/Front/Content/Product/list.html.twig', [
			'productDetails' => $paginationResult->getResults(),
			'orderingSetting' => $orderingSetting,
			'paginationResult' => $paginationResult,
			'category' => $category,
			'filterForm' => $filterForm->createView(),
			'filterFormSubmited' => $filterForm->isSubmitted(),
			'domainId' => $domain->getId(),
		]);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $page
	 */
	public function searchAction(Request $request, $page) {
		$productOnCurrentDomainFacade = $this->get('ss6.shop.product.product_on_current_domain_facade');
		/* @var $productOnCurrentDomainFacade \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade */
		$productListOrderingService = $this->get('ss6.shop.product.product_list_ordering_service');
		/* @var $productListOrderingService \SS6\ShopBundle\Model\Product\ProductListOrderingService */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$productFilterFormTypeFactory = $this->get('ss6.shop.form.front.product.product_filter_form_type_factory');
		/* @var $productFilterFormTypeFactory \SS6\ShopBundle\Form\Front\Product\ProductFilterFormTypeFactory */
		$categoryFacade = $this->get(CategoryFacade::class);
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */

		$searchText = $request->query->get('q');

		if ($page === '1') {
			return $this->redirect($this->generateUrl('front_product_search', $request->query->all()));
		}

		$orderingSetting = $productListOrderingService->getOrderingSettingFromRequest($request);

		$productFilterData = new ProductFilterData();

		$filterForm = $this->createForm($productFilterFormTypeFactory->createForSearch(
			$domain->getId(),
			$domain->getLocale(),
			$searchText
		));
		$filterForm->setData($productFilterData);
		$filterForm->handleRequest($request);

		$foundCategories = $categoryFacade->getVisibleByDomainAndSearchText(
			$domain->getId(),
			$domain->getLocale(),
			$searchText
		);

		$paginationResult = $productOnCurrentDomainFacade->getPaginatedProductDetailsForSearch(
			$searchText,
			$productFilterData,
			$orderingSetting,
			$page,
			self::PRODUCTS_PER_PAGE
		);

		return $this->render('@SS6Shop/Front/Content/Product/search.html.twig', [
			'searchText' => $searchText,
			'foundCategories' => $foundCategories,
			'productDetails' => $paginationResult->getResults(),
			'orderingSetting' => $orderingSetting,
			'paginationResult' => $paginationResult,
			'filterForm' => $filterForm->createView(),
			'filterFormSubmited' => $filterForm->isSubmitted(),
			'domainId' => $domain->getId(),
		]);
	}

	public function selectOrderingModeAction(Request $request) {
		$productListOrderingService = $this->get('ss6.shop.product.product_list_ordering_service');
		/* @var $productListOrderingService \SS6\ShopBundle\Model\Product\ProductListOrderingService */

		$orderingSetting = $productListOrderingService->getOrderingSettingFromRequest($request);
		$form = $this->createForm(new OrderingSettingFormType());
		$form->setData(['orderingMode' => $orderingSetting->getOrderingMode()]);

		return $this->render('@SS6Shop/Front/Content/Product/orderingSetting.html.twig', [
			'form' => $form->createView(),
			'cookieName' => ProductListOrderingService::COOKIE_NAME,
		]);
	}

}

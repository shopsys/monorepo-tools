<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Product\OrderingSettingFormType;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\ProductListOrderingService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller {

	const PRODUCTS_PER_PAGE = 12;

	/**
	 * @param int $id
	 */
	public function detailAction($id) {
		$productOnCurrentDomainFacade = $this->get('ss6.shop.product.product_on_current_domain_facade');
		/* @var $productOnCurrentDomainFacade \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade */

		$productDetail = $productOnCurrentDomainFacade->getVisibleProductDetailById($id);

		return $this->render('@SS6Shop/Front/Content/Product/detail.html.twig', [
			'productDetail' => $productDetail,
		]);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $categoryId
	 * @param int $page
	 */
	public function listByCategoryAction(Request $request, $categoryId, $page) {
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

		$category = $categoryFacade->getById($categoryId);

		$orderingSetting = $productListOrderingService->getOrderingSettingFromRequest($request);

		$productFilterData = new ProductFilterData();

		$filterForm = $this->createForm($productFilterFormTypeFactory->create($domain->getId(), $domain->getLocale(), $category));
		$filterForm->setData($productFilterData);
		$filterForm->handleRequest($request);

		$paginationResult = $productOnCurrentDomainFacade->getPaginatedProductDetailsInCategory(
			$productFilterData,
			$orderingSetting,
			$page,
			self::PRODUCTS_PER_PAGE,
			$categoryId
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

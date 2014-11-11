<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Product\OrderingSettingFormType;
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

		return $this->render('@SS6Shop/Front/Content/Product/detail.html.twig', array(
			'productDetail' => $productDetail,
		));
	}

	public function listAction(Request $request, $page) {
		$productOnCurrentDomainFacade = $this->get('ss6.shop.product.product_on_current_domain_facade');
		/* @var $productOnCurrentDomainFacade \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade */
		$productListOrderingService = $this->get('ss6.shop.product.product_list_ordering_service');
		/* @var $productListOrderingService \SS6\ShopBundle\Model\Product\ProductListOrderingService */

		$orderingSetting = $productListOrderingService->getOrderingSettingFromRequest($request);

		$paginationResult = $productOnCurrentDomainFacade->getPaginationResult($orderingSetting, $page, self::PRODUCTS_PER_PAGE);
		$productDetails = $productOnCurrentDomainFacade
			->getPaginatedProductDetailsForProductList($orderingSetting, $page, self::PRODUCTS_PER_PAGE);

		return $this->render('@SS6Shop/Front/Content/Product/list.html.twig', array(
			'productDetails' => $productDetails,
			'orderingSetting' => $orderingSetting,
			'paginationResult' => $paginationResult,
		));
	}

	public function selectOrderingModeAction(Request $request) {
		$productListOrderingService = $this->get('ss6.shop.product.product_list_ordering_service');
		/* @var $productListOrderingService \SS6\ShopBundle\Model\Product\ProductListOrderingService */

		$orderingSetting = $productListOrderingService->getOrderingSettingFromRequest($request);
		$form = $this->createForm(new OrderingSettingFormType());
		$form->setData(array('orderingMode' => $orderingSetting->getOrderingMode()));

		return $this->render('@SS6Shop/Front/Content/Product/orderingSetting.html.twig', array(
			'form' => $form->createView(),
			'cookieName' => ProductListOrderingService::COOKIE_NAME,
		));
	}

}

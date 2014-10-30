<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Model\Product\ProductListOrderingSetting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends Controller {

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

	public function listAction() {
		$productOnCurrentDomainFacade = $this->get('ss6.shop.product.product_on_current_domain_facade');
		/* @var $productOnCurrentDomainFacade \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade */

		$orderingSetting = new ProductListOrderingSetting();

		$productDetails = $productOnCurrentDomainFacade->getProductDetailsForProductList($orderingSetting);

		return $this->render('@SS6Shop/Front/Content/Product/list.html.twig', array(
			'productDetails' => $productDetails,
			'orderingSetting' => $orderingSetting,
		));
	}

}

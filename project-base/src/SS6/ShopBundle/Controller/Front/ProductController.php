<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends Controller {
	
	/**
	 * @param int $id
	 */
	public function detailAction($id) {
		$productRepository = $this->get('ss6.shop.product.product_repository');
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$productDetailFactory = $this->get('ss6.shop.product.product_detail_factory');
		/* @var $productDetailFactory \SS6\ShopBundle\Model\Product\Detail\Factory */
			
		$product = $productRepository->getVisibleById($id);
		$productDetail = $productDetailFactory->getDetailForProduct($product);
		
		return $this->render('@SS6Shop/Front/Content/Product/detail.html.twig', array(
			'productDetail' => $productDetail,
		));
	}
	
	public function listAction() {
		$productRepository = $this->get('ss6.shop.product.product_repository');
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$productDetailFactory = $this->get('ss6.shop.product.product_detail_factory');
		/* @var $productDetailFactory \SS6\ShopBundle\Model\Product\Detail\Factory */
			
		$products = $productRepository->findAllVisible();
		$productDetails = $productDetailFactory->getDetailsForProducts($products);
		
		return $this->render('@SS6Shop/Front/Content/Product/list.html.twig', array(
			'productDetails' => $productDetails,
		));
	}
}

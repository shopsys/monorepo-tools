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
			
		$product = $productRepository->getVisibleById($id);
		
		return $this->render('@SS6Shop/Front/Content/Product/detail.html.twig', array(
			'product' => $product,
		));
	}
	
	public function listAction() {
		$productRepository = $this->get('ss6.shop.product.product_repository');
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
			
		$products = $productRepository->findAllVisible();
		
		return $this->render('@SS6Shop/Front/Content/Product/list.html.twig', array(
			'products' => $products,
		));
	}
}

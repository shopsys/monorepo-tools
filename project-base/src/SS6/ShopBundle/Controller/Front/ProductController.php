<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Model\Product\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends Controller {
	public function detailAction($id) {
		$productRepository = $this->get('ss6.core.product.product_repository');
		/* @var $productRepository ProductRepository */
			
		$product = $productRepository->findVisibleById($id);
		if (!$product) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Product not found');
		}
		
		return $this->render('@SS6Shop/Front/Content/Product/detail.html.twig', array(
			'product' => $product,
		));
	}
}

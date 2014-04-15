<?php

namespace SS6\FrontBundle\Controller;

use SS6\ShopBundle\Model\Product\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller {
	public function detailAction($id) {
		$productRepository = $this->get('ss6.core.product.product_repository');
		/* @var $productRepository ProductRepository */
			
		$product = $productRepository->findVisibleById($id);
		if (!$product) {
			throw new NotFoundHttpException('Product not found');
		}
		
		return $this->render('SS6FrontBundle:Content:Product/detail.html.twig', array(
			'product' => $product,
		));
	}
}

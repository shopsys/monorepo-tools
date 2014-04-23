<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Cart\AddProductFormType;
use SS6\ShopBundle\Model\Cart\AddProductResult;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller {

	public function boxAction() {
		$cart = $this->get('ss6.shop.cart');
		/* @var $cart \SS6\ShopBundle\Model\Cart\Cart */
		
		return $this->render('@SS6Shop/Front/Inline/Cart/cartBox.html.twig', array(
			'cart' => $cart,
		));
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function addProductFormAction(Product $product) {
		$formData = array('product_id' => $product->getId());
		$form = $this->createForm(new AddProductFormType(), $formData, array(
			'action' =>	$this->get('router')->generate('front_cart_add_product', array(), true),
			'method' => 'POST',
		));
		
		return $this->render('@SS6Shop/Front/Inline/Cart/addProduct.html.twig', array(
			'form' => $form->createView(),
		));
	}
	
	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function addProductAction(Request $request) {
		$form = $this->createForm(new AddProductFormType(), null, array(
			'method' => 'POST',
		));
		$form->handleRequest($request);
		
		if ($request->isXmlHttpRequest()) {
			$response = new JsonResponse();
			
			return $response;
		} else {
			if ($form->isValid()) {
				try {
					$formData = $form->getData();
					$cartFacade = $this->get('ss6.shop.cart.cart_facade');
					/* @var $cartFacade \SS6\ShopBundle\Model\Cart\CartFacade */
					$addProductResult = $cartFacade->addProductToCart($formData['product_id'], $formData['quantity']);
					
					$this->get('session')->getFlashBag()->add(
						'success', $this->getAddProductResultMessage($addProductResult)
					);
				} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $ex) {
					throw $this->createNotFoundException('Product not found', $ex);
				}
			}
		}
		
		if ($this->getRequest()->headers->get('referer')) {
			$redirectTo = $this->getRequest()->headers->get('referer');
		} else {
			$redirectTo = $this->get('router')->generate('front_homepage', array(), true);
		}
		
		return $this->redirect($redirectTo);
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Cart\AddProductResult $addProductResult
	 * @return string
	 */
	private function getAddProductResultMessage(AddProductResult $addProductResult) {
		$productName = $addProductResult->getCartItem()->getProduct()->getName();
		if ($addProductResult->getIsNew()) {
			$message = sprintf('Do košíku bylo vloženo zboží <b>%s</b> (%d ks)', 
				htmlentities($productName, ENT_QUOTES),
				$addProductResult->getAddedQuantity());
		} else {
			$message = sprintf('Do košíku bylo vloženo zboží <b>%s</b> (celkem již %d ks)', 
				htmlentities($productName, ENT_QUOTES),
				$addProductResult->getCartItem()->getQuantity());
		}
		return $message;
	}

}

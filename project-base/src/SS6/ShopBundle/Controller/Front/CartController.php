<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Cart\AddProductFormType;
use SS6\ShopBundle\Model\Cart\AddProductResult;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller {

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function indexAction(Request $request) {
		$cart = $this->get('ss6.shop.cart');
		/* @var $cart \SS6\ShopBundle\Model\Cart\Cart */

		$cartFormData = array(
			'quantities' => array(),
		);
		foreach ($cart->getItems() as $cartItem) {
			$cartFormData['quantities'][$cartItem->getId()] = $cartItem->getQuantity();
		}

		$form = $this->createForm(new \SS6\ShopBundle\Form\Front\Cart\CartFormType($cart));
		$form->setData($cartFormData);
		$form->handleRequest($request);
		$invalidCartRecalc = false;

		if ($form->isValid()) {
			$cartFacade = $this->get('ss6.shop.cart.cart_facade');
			/* @var $cartFacade \SS6\ShopBundle\Model\Cart\CartFacade */
			try {
				$cartFacade->changeQuantities($form->getData()['quantities']);
				$this->get('session')->getFlashBag()->add(
					'success', 'Množství v košíku bylo úspěšně přepočítáno.'
				);
			} catch (\SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException $ex) {
				$invalidCartRecalc = true;
			}
			return $this->redirect($this->generateUrl('front_cart'));
		} elseif ($form->isSubmitted()) {
			$invalidCartRecalc = true;
		}

		if ($invalidCartRecalc) {
			$form->addError(new FormError('Prosím zkontrolujte, zda jste správně zadali množství kusů veškerých položek v košíku.'));
		}

		return $this->render('@SS6Shop/Front/Content/Cart/index.html.twig', array(
			'backUrl' => $this->getBackUrl($request),
			'cart' => $cart,
			'cartItems' => $cart->getItems(),
			'form' => $form->createView(),
		));
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return string
	 */
	private function getBackUrl(Request $request) {
		$refferer = $request->server->get('HTTP_REFERER');
		$cartUrl = $this->generateUrl('front_cart', array(), true);
		if ($refferer !== null && $refferer !== $cartUrl) {
			return $refferer;
		} else {
			return $this->generateUrl('front_homepage', array(), true);
		}
	}

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
		$formData = array('productId' => $product->getId());
		$form = $this->createForm(new AddProductFormType(), $formData, array(
			'action' => $this->generateUrl('front_cart_add_product'),
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
		
		$actionResult = array('success' => false, 'message' => 'Zadejte prosím platné množství kusů, které chcete vložit do košíku.');
		if ($form->isValid()) {
			try {
				$formData = $form->getData();
				$cartFacade = $this->get('ss6.shop.cart.cart_facade');
				/* @var $cartFacade \SS6\ShopBundle\Model\Cart\CartFacade */
				$addProductResult = $cartFacade->addProductToCart($formData['productId'], (int)$formData['quantity']);
				$actionResult['success'] = true;
				$actionResult['message'] = $this->getAddProductResultMessage($addProductResult);
			} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $ex) {
				$actionResult['success'] = false;
				$actionResult['message'] = 'Zvolené zboží již není v nabídce nebo neexistuje.';
			} catch (\SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException $ex) {
				$actionResult['success'] = false;
				$actionResult['message'] = 'Zadejte prosím platné množství kusů, které chcete vložit do košíku.';
			} catch (\SS6\ShopBundle\Model\Cart\Exception\CartException $ex) {
				$actionResult['success'] = false;
				$actionResult['message'] = 'Zboží se nepodařilo vložit do košíku.';
			}
		}
		if ($request->isXmlHttpRequest()) {
			return $this->getAjaxAddProductResponse($actionResult);
		}
		
		$this->get('session')->getFlashBag()->add(
			$actionResult['success'] ? 'success' : 'error', 
			$actionResult['message']
		);
		
		if ($this->getRequest()->headers->get('referer')) {
			$redirectTo = $this->getRequest()->headers->get('referer');
		} else {
			$redirectTo = $this->generateUrl('front_homepage');
		}
		
		return $this->redirect($redirectTo);
	}
	
	/**
	 * @param array $actionResult
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	private function getAjaxAddProductResponse(array $actionResult) {
		$engine = $this->container->get('templating');
		$actionResult['jsWindowId'] = 'productAddResponse';
		$actionResult['jsWindow'] = $engine->render('@SS6Shop/Front/Inline/jsWindow.html.twig', array(
			'id' => $actionResult['jsWindowId'],
			'text' => $actionResult['message'],
			'noEscape' => true,
			'continueButton' => $actionResult['success'],
			'continueButtonText' => 'Pokračovat do košíku',
			'continueUrl' => $this->generateUrl('front_cart'),
		));
		$actionResult['cartBoxReloadUrl'] = $this->generateUrl('front_cart_box');
		return new JsonResponse($actionResult);
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Cart\AddProductResult $addProductResult
	 * @return string
	 */
	private function getAddProductResultMessage(AddProductResult $addProductResult) {
		$productName = $addProductResult->getCartItem()->getName();
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

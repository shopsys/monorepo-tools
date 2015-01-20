<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Cart\AddProductFormType;
use SS6\ShopBundle\Form\Front\Cart\CartFormType;
use SS6\ShopBundle\Model\Cart\AddProductResult;
use SS6\ShopBundle\Model\FlashMessage\FlashMessageSender;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller {

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function indexAction(Request $request) {
		$cart = $this->get('ss6.shop.cart');
		/* @var $cart \SS6\ShopBundle\Model\Cart\Cart */
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$cartItemPriceCalculation = $this->get('ss6.shop.cart.item.cart_item_price_calculation');
		/* @var $cartItemPriceCalculation \SS6\ShopBundle\Model\Cart\Item\CartItemPriceCalculation */
		$cartSummaryCalculation = $this->get('ss6.shop.cart.cart_summary_calculation');
		/* @var $cartSummaryCalculation \SS6\ShopBundle\Model\Cart\CartSummaryCalculation */

		$cartFormData = [
			'quantities' => [],
		];
		foreach ($cart->getItems() as $cartItem) {
			$cartFormData['quantities'][$cartItem->getId()] = $cartItem->getQuantity();
		}

		$form = $this->createForm(new CartFormType($cart));
		$form->setData($cartFormData);
		$form->handleRequest($request);
		$invalidCartRecalc = false;

		if ($form->isValid()) {
			$cartFacade = $this->get('ss6.shop.cart.cart_facade');
			/* @var $cartFacade \SS6\ShopBundle\Model\Cart\CartFacade */
			try {
				$cartFacade->changeQuantities($form->getData()['quantities']);
			} catch (\SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException $ex) {
				$invalidCartRecalc = true;
			}

			if (!$invalidCartRecalc) {
				if ($form->get('recalcToOrder')->isClicked()) {
					return $this->redirect($this->generateUrl('front_order_index'));
				} else {
					$flashMessageSender->addSuccessFlash('Počet kusů položek v košíku byl úspěšně přepočítán.');
					return $this->redirect($this->generateUrl('front_cart'));
				}
			}
		} elseif ($form->isSubmitted()) {
			$invalidCartRecalc = true;
		}

		if ($invalidCartRecalc) {
			$flashMessageSender->addErrorFlash('Prosím zkontrolujte, zda jste správně zadali množství kusů veškerých položek v košíku.');
		}

		$cartItems = $cart->getItems();
		$cartItemPrices = $cartItemPriceCalculation->calculatePrices($cartItems);
		$cartSummary = $cartSummaryCalculation->calculateSummary($cart);

		return $this->render('@SS6Shop/Front/Content/Cart/index.html.twig', [
			'cart' => $cart,
			'cartItems' => $cartItems,
			'cartItemPrices' => $cartItemPrices,
			'cartSummary' => $cartSummary,
			'form' => $form->createView(),
		]);
	}

	public function boxAction() {
		$cart = $this->get('ss6.shop.cart');
		/* @var $cart \SS6\ShopBundle\Model\Cart\Cart */
		$cartSummaryCalculation = $this->get('ss6.shop.cart.cart_summary_calculation');
		/* @var $cartSummaryCalculation \SS6\ShopBundle\Model\Cart\CartSummaryCalculation */

		$cartSummary = $cartSummaryCalculation->calculateSummary($cart);

		return $this->render('@SS6Shop/Front/Inline/Cart/cartBox.html.twig', [
			'cart' => $cart,
			'cartSummary' => $cartSummary,
		]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function addProductFormAction(Product $product) {
		$formData = ['productId' => $product->getId()];
		$form = $this->createForm(new AddProductFormType(), $formData, [
			'action' => $this->generateUrl('front_cart_add_product'),
			'method' => 'POST',
		]);

		return $this->render('@SS6Shop/Front/Inline/Cart/addProduct.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function addProductAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$flashMessageBag = $this->get('ss6.shop.flash_message.bag.front');
		/* @var $flashMessageBag \SS6\ShopBundle\Model\FlashMessage\Bag */

		$form = $this->createForm(new AddProductFormType(), null, [
			'method' => 'POST',
		]);
		$form->handleRequest($request);

		$actionResult = ['success' => false, 'message' => 'Zadejte prosím platné množství kusů, které chcete vložit do košíku.'];
		if ($form->isValid()) {
			try {
				$formData = $form->getData();
				$cartFacade = $this->get('ss6.shop.cart.cart_facade');
				/* @var $cartFacade \SS6\ShopBundle\Model\Cart\CartFacade */
				$addProductResult = $cartFacade->addProductToCart($formData['productId'], (int)$formData['quantity']);

				$this->sendAddProductResultFlashMessage($flashMessageSender, $addProductResult);
			} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $ex) {
				$flashMessageSender->addErrorFlash('Zvolené zboží již není v nabídce nebo neexistuje.');
			} catch (\SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException $ex) {
				$flashMessageSender->addErrorFlash('Zadejte prosím platné množství kusů, které chcete vložit do košíku.');
			} catch (\SS6\ShopBundle\Model\Cart\Exception\CartException $ex) {
				$flashMessageSender->addErrorFlash('Zboží se nepodařilo vložit do košíku.');
			}
		}

		if ($request->isXmlHttpRequest()) {
			$errorMessages = $flashMessageBag->getErrorMessages();
			if (count($errorMessages) === 0) {
				$actionResult['success'] = true;
				$actionResult['message'] = $flashMessageBag->getSuccessMessages();
			} else {
				$actionResult['success'] = false;
				$actionResult['message'] = $errorMessages;
			}

			return $this->getAjaxAddProductResponse($actionResult);
		}

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
		$actionResult['continueUrl'] = $this->generateUrl('front_cart');
		$actionResult['cartBoxReloadUrl'] = $this->generateUrl('front_cart_box');

		return new JsonResponse($actionResult);
	}

	/**
	 * @param \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender $flashMessageSender
	 * @param \SS6\ShopBundle\Model\Cart\AddProductResult $addProductResult
	 */
	private function sendAddProductResultFlashMessage(
		FlashMessageSender $flashMessageSender,
		AddProductResult $addProductResult
	) {
		if ($addProductResult->getIsNew()) {
			$flashMessageSender->addSuccessFlashTwig(
				'Do košíku bylo vloženo zboží <strong>{{ name }}</strong> ({{ quantity }} ks)',
				[
					'name' => $addProductResult->getCartItem()->getName(),
					'quantity' => $addProductResult->getAddedQuantity(),
				]
			);
		} else {
			$flashMessageSender->addSuccessFlashTwig(
				'Do košíku bylo vloženo zboží <strong>{{ name }}</strong> (celkem již {{ quantity }} ks)',
				[
					'name' => $addProductResult->getCartItem()->getName(),
					'quantity' => $addProductResult->getCartItem()->getQuantity(),
				]
			);
		}
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $cartItemId
	 */
	public function deleteAction(Request $request, $cartItemId) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$cartItemId = (int)$cartItemId;
		$token = $request->query->get('_token');

		if ($this->get('form.csrf_provider')->isCsrfTokenValid('front_cart_delete_' . $cartItemId, $token)) {
			$cartFacade = $this->get('ss6.shop.cart.cart_facade');
			/* @var $cartFacade \SS6\ShopBundle\Model\Cart\CartFacade */
			try {
				$productName = $cartFacade->getProductByCartItemId($cartItemId)->getName();
				$cartFacade->deleteCartItem($cartItemId);
				$flashMessageSender->addSuccessFlashTwig(
					'Z košíku bylo ostraněno zboží {{ name }}',
					['name' => $productName]
				);
			} catch (\SS6\ShopBundle\Model\Cart\Exception\InvalidCartItemException $ex) {
				$flashMessageSender->addErrorFlash('Nepodařilo se odstranit položku z košíku. Nejspíš je již odstraněno');
			}
		} else {
			$flashMessageSender->addErrorFlash('Nepodařilo se odstranit položku z košíku.
					Zřejmě vypršela platnost odkazu pro jeho smazání, proto to vyzkoušejte ještě jednou.');
		}

		return $this->redirect($this->generateUrl('front_cart'));
	}

}

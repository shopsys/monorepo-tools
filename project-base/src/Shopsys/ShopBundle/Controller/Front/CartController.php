<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\ErrorService;
use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\Front\Cart\AddProductFormType;
use Shopsys\ShopBundle\Form\Front\Cart\CartFormType;
use Shopsys\ShopBundle\Model\Cart\AddProductResult;
use Shopsys\ShopBundle\Model\Cart\CartFacade;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Module\ModuleList;
use Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory;
use Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessoryFacade;
use Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\TransportAndPayment\FreeTransportAndPaymentFacade;
use Symfony\Component\HttpFoundation\Request;

class CartController extends FrontBaseController {

	const AFTER_ADD_WINDOW_ACCESORIES_LIMIT = 3;

	const RECALCULATE_ONLY_PARAMETER_NAME = 'recalculateOnly';

	/**
	 * @var \Shopsys\ShopBundle\Model\Cart\CartFacade
	 */
	private $cartFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessoryFacade
	 */
	private $productAccessoryFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory
	 */
	private $productDetailFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\TransportAndPayment\FreeTransportAndPaymentFacade
	 */
	private $freeTransportAndPaymentFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory
	 */
	private $orderPreviewFactory;

	/**
	 * @var \Shopsys\ShopBundle\Component\Controller\ErrorService
	 */
	private $errorService;

	public function __construct(
		ProductAccessoryFacade $productAccessoryFacade,
		CartFacade $cartFacade,
		CurrentCustomer $currentCustomer,
		Domain $domain,
		FreeTransportAndPaymentFacade $freeTransportAndPaymentFacade,
		ProductDetailFactory $productDetailFactory,
		OrderPreviewFactory $orderPreviewFactory,
		ErrorService $errorService
	) {
		$this->productAccessoryFacade = $productAccessoryFacade;
		$this->cartFacade = $cartFacade;
		$this->currentCustomer = $currentCustomer;
		$this->domain = $domain;
		$this->freeTransportAndPaymentFacade = $freeTransportAndPaymentFacade;
		$this->productDetailFactory = $productDetailFactory;
		$this->orderPreviewFactory = $orderPreviewFactory;
		$this->errorService = $errorService;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function indexAction(Request $request) {
		$cart = $this->cartFacade->getCartOfCurrentCustomer();

		if ($cart->isEmpty()) {
			$this->cartFacade->cleanAdditionalData();
		}

		$cartFormData = [
			'quantities' => [],
		];
		foreach ($cart->getItems() as $cartItem) {
			$cartFormData['quantities'][$cartItem->getId()] = $cartItem->getQuantity();
		}

		$form = $this->createForm(new CartFormType($cart));
		$form->setData($cartFormData);
		$form->handleRequest($request);
		$invalidCart = false;

		if ($form->isValid()) {
			try {
				$this->cartFacade->changeQuantities($form->getData()['quantities']);

				if (!$request->get(self::RECALCULATE_ONLY_PARAMETER_NAME, false)) {
					return $this->redirectToRoute('front_order_index');
				}
			} catch (\Shopsys\ShopBundle\Model\Cart\Exception\InvalidQuantityException $ex) {
				$invalidCart = true;
			}
		} elseif ($form->isSubmitted()) {
			$invalidCart = true;
		}

		if ($invalidCart) {
			$this->getFlashMessageSender()->addErrorFlash(
				t('Please make sure that you entered right quantity of all items in cart.')
			);
		}

		$cartItems = $cart->getItems();
		$domainId = $this->domain->getId();

		$orderPreview = $this->orderPreviewFactory->createForCurrentUser();
		$productsPrice = $orderPreview->getProductsPrice();
		$remainingPriceWithVat = $this->freeTransportAndPaymentFacade->getRemainingPriceWithVat(
			$productsPrice->getPriceWithVat(),
			$domainId
		);

		return $this->render('@ShopsysShop/Front/Content/Cart/index.html.twig', [
			'cart' => $cart,
			'cartItems' => $cartItems,
			'cartItemPrices' => $orderPreview->getQuantifiedItemsPrices(),
			'form' => $form->createView(),
			'isFreeTransportAndPaymentActive' => $this->freeTransportAndPaymentFacade->isActive($domainId),
			'isPaymentAndTransportFree' => $this->freeTransportAndPaymentFacade->isFree($productsPrice->getPriceWithVat(), $domainId),
			'remainingPriceWithVat' => $remainingPriceWithVat,
			'cartItemDiscounts' => $orderPreview->getQuantifiedItemsDiscounts(),
			'productsPrice' => $productsPrice,
		]);
	}

	public function boxAction() {
		$orderPreview = $this->orderPreviewFactory->createForCurrentUser();

		return $this->render('@ShopsysShop/Front/Inline/Cart/cartBox.html.twig', [
			'cart' => $this->cartFacade->getCartOfCurrentCustomer(),
			'productsPrice' => $orderPreview->getProductsPrice(),
		]);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param string $type
	 */
	public function addProductFormAction(Product $product, $type = 'normal') {
		$formData = ['productId' => $product->getId()];
		$form = $this->createForm(new AddProductFormType(), $formData, [
			'action' => $this->generateUrl('front_cart_add_product'),
			'method' => 'POST',
		]);

		return $this->render('@ShopsysShop/Front/Inline/Cart/addProduct.html.twig', [
			'form' => $form->createView(),
			'product' => $product,
			'type' => $type,
		]);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function addProductAction(Request $request) {
		$form = $this->createForm(new AddProductFormType(), null, [
			'method' => 'POST',
		]);
		$form->handleRequest($request);

		if ($form->isValid()) {
			try {
				$formData = $form->getData();

				$addProductResult = $this->cartFacade->addProductToCart($formData['productId'], (int)$formData['quantity']);

				$this->sendAddProductResultFlashMessage($addProductResult);
			} catch (\Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Selected product no longer available or doesn\'t exist.'));
			} catch (\Shopsys\ShopBundle\Model\Cart\Exception\InvalidQuantityException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Please enter valid quantity you want to add to cart.'));
			} catch (\Shopsys\ShopBundle\Model\Cart\Exception\CartException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Unable to add product to cart'));
			}
		} else {
			// Form errors list in flash message is temporary solution.
			// We need to determine couse of error when adding product to cart.
			$flashMessageBag = $this->get('shopsys.shop.component.flash_message.bag.front');
			$formErrors = $this->errorService->getAllErrorsAsArray($form, $flashMessageBag);
			$this->getFlashMessageSender()->addErrorFlashTwig(
				t('Unable to add product to cart:<br/><ul><li>{{ errors|raw }}</li></ul>'),
				[
					'errors' => implode('</li><li>', $formErrors),
				]
			);
		}

		if ($this->getRequest()->headers->get('referer')) {
			$redirectTo = $this->getRequest()->headers->get('referer');
		} else {
			$redirectTo = $this->generateUrl('front_homepage');
		}

		return $this->redirect($redirectTo);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function addProductAjaxAction(Request $request) {
		$form = $this->createForm(new AddProductFormType(), null, [
			'method' => 'POST',
		]);
		$form->handleRequest($request);

		if ($form->isValid()) {
			try {
				$formData = $form->getData();

				$addProductResult = $this->cartFacade->addProductToCart($formData['productId'], (int)$formData['quantity']);

				$this->sendAddProductResultFlashMessage($addProductResult);

				$accessories = $this->productAccessoryFacade->getTopOfferedAccessories(
					$addProductResult->getCartItem()->getProduct(),
					$this->domain->getId(),
					$this->currentCustomer->getPricingGroup(),
					self::AFTER_ADD_WINDOW_ACCESORIES_LIMIT
				);
				$accessoryDetails = $this->productDetailFactory->getDetailsForProducts($accessories);

				return $this->render('@ShopsysShop/Front/Inline/Cart/afterAddWindow.html.twig', [
					'accessoryDetails' => $accessoryDetails,
					'ACCESSORIES_ON_BUY' => ModuleList::ACCESSORIES_ON_BUY,
				]);
			} catch (\Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Selected product no longer available or doesn\'t exist.'));
			} catch (\Shopsys\ShopBundle\Model\Cart\Exception\InvalidQuantityException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Please enter valid quantity you want to add to cart.'));
			} catch (\Shopsys\ShopBundle\Model\Cart\Exception\CartException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Unable to add product to cart'));
			}
		} else {
			// Form errors list in flash message is temporary solution.
			// We need to determine couse of error when adding product to cart.
			$flashMessageBag = $this->get('shopsys.shop.component.flash_message.bag.front');
			$formErrors = $this->errorService->getAllErrorsAsArray($form, $flashMessageBag);
			$this->getFlashMessageSender()->addErrorFlashTwig(
				t('Unable to add product to cart:<br/><ul><li>{{ errors|raw }}</li></ul>'),
				[
					'errors' => implode('</li><li>', $formErrors),
				]
			);
		}

		return $this->forward('ShopsysShopBundle:Front/FlashMessage:index');
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Cart\AddProductResult $addProductResult
	 */
	private function sendAddProductResultFlashMessage(
		AddProductResult $addProductResult
	) {
		if ($addProductResult->getIsNew()) {
			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Product <strong>{{ name }}</strong> ({{ quantity|formatNumber }} {{ unitName }}) added to the cart'),
				[
					'name' => $addProductResult->getCartItem()->getName(),
					'quantity' => $addProductResult->getAddedQuantity(),
					'unitName' => $addProductResult->getCartItem()->getProduct()->getUnit()->getName(),
				]
			);
		} else {
			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Product <strong>{{ name }}</strong> added to the cart (total amount {{ quantity|formatNumber }} {{ unitName }})'),
				[
					'name' => $addProductResult->getCartItem()->getName(),
					'quantity' => $addProductResult->getCartItem()->getQuantity(),
					'unitName' => $addProductResult->getCartItem()->getProduct()->getUnit()->getName(),
				]
			);
		}
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $cartItemId
	 */
	public function deleteAction(Request $request, $cartItemId) {
		$cartItemId = (int)$cartItemId;
		$token = $request->query->get('_token');

		if ($this->get('form.csrf_provider')->isCsrfTokenValid('front_cart_delete_' . $cartItemId, $token)) {
			try {
				$productName = $this->cartFacade->getProductByCartItemId($cartItemId)->getName();

				$this->cartFacade->deleteCartItem($cartItemId);

				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Product {{ name }} removed from cart'),
					['name' => $productName]
				);
			} catch (\Shopsys\ShopBundle\Model\Cart\Exception\InvalidCartItemException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Unable to remove item from cart. The item is probably already removed.'));
			}
		} else {
			$this->getFlashMessageSender()->addErrorFlash(
				t('Unable to remove item from cart. The link for removing it probably expired, try it again.'
				)
			);
		}

		return $this->redirectToRoute('front_cart');
	}

}

<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\ErrorService;
use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Form\Front\Cart\AddProductFormType;
use SS6\ShopBundle\Form\Front\Cart\CartFormType;
use SS6\ShopBundle\Model\Cart\AddProductResult;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\CartFacade;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Module\ModuleList;
use SS6\ShopBundle\Model\Order\Preview\OrderPreviewFactory;
use SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryFacade;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\TransportAndPayment\FreeTransportAndPaymentFacade;
use Symfony\Component\HttpFoundation\Request;

class CartController extends FrontBaseController {

	const AFTER_ADD_WINDOW_ACCESORIES_LIMIT = 3;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Cart
	 */
	private $cart;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\CartFacade
	 */
	private $cartFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryFacade
	 */
	private $productAccessoryFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory
	 */
	private $productDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Model\TransportAndPayment\FreeTransportAndPaymentFacade
	 */
	private $freeTransportAndPaymentFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Preview\OrderPreviewFactory
	 */
	private $orderPreviewFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Controller\ErrorService
	 */
	private $errorService;

	public function __construct(
		ProductAccessoryFacade $productAccessoryFacade,
		CartFacade $cartFacade,
		CurrentCustomer $currentCustomer,
		Domain $domain,
		FreeTransportAndPaymentFacade $freeTransportAndPaymentFacade,
		ProductDetailFactory $productDetailFactory,
		Cart $cart,
		OrderPreviewFactory $orderPreviewFactory,
		ErrorService $errorService
	) {
		$this->productAccessoryFacade = $productAccessoryFacade;
		$this->cartFacade = $cartFacade;
		$this->currentCustomer = $currentCustomer;
		$this->domain = $domain;
		$this->freeTransportAndPaymentFacade = $freeTransportAndPaymentFacade;
		$this->productDetailFactory = $productDetailFactory;
		$this->cart = $cart;
		$this->orderPreviewFactory = $orderPreviewFactory;
		$this->errorService = $errorService;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function indexAction(Request $request) {
		$cartFormData = [
			'quantities' => [],
		];
		foreach ($this->cart->getItems() as $cartItem) {
			$cartFormData['quantities'][$cartItem->getId()] = $cartItem->getQuantity();
		}

		$form = $this->createForm(new CartFormType($this->cart));
		$form->setData($cartFormData);
		$form->handleRequest($request);
		$invalidCart = false;

		if ($form->isValid()) {
			try {
				$this->cartFacade->changeQuantities($form->getData()['quantities']);
			} catch (\SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException $ex) {
				$invalidCart = true;
			}

			if (!$invalidCart) {
				if ($form->get('recalcToOrder')->isClicked()) {
					return $this->redirectToRoute('front_order_index');
				} else {
					$this->getFlashMessageSender()->addSuccessFlash(t('Množství položek v košíku bylo úspěšně přepočítáno.'));
					return $this->redirectToRoute('front_cart');
				}
			}
		} elseif ($form->isSubmitted()) {
			$invalidCart = true;
		}

		if ($invalidCart) {
			$this->getFlashMessageSender()->addErrorFlash(
				t('Prosím zkontrolujte, zda jste správně zadali množství veškerých položek v košíku.')
			);
		}

		$cartItems = $this->cart->getItems();
		$domainId = $this->domain->getId();

		$orderPreview = $this->orderPreviewFactory->createForCurrentUser();
		$productsPrice = $orderPreview->getProductsPrice();
		$remainingPriceWithVat = $this->freeTransportAndPaymentFacade->getRemainingPriceWithVat(
			$productsPrice->getPriceWithVat(),
			$domainId
		);

		return $this->render('@SS6Shop/Front/Content/Cart/index.html.twig', [
			'cart' => $this->cart,
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

		return $this->render('@SS6Shop/Front/Inline/Cart/cartBox.html.twig', [
			'cart' => $this->cart,
			'productsPrice' => $orderPreview->getProductsPrice(),
		]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param string $type
	 */
	public function addProductFormAction(Product $product, $type = 'normal') {
		$formData = ['productId' => $product->getId()];
		$form = $this->createForm(new AddProductFormType(), $formData, [
			'action' => $this->generateUrl('front_cart_add_product'),
			'method' => 'POST',
		]);

		return $this->render('@SS6Shop/Front/Inline/Cart/addProduct.html.twig', [
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
			} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Zvolené zboží již není v nabídce nebo neexistuje.'));
			} catch (\SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Zadejte prosím platné množství, které chcete vložit do košíku.'));
			} catch (\SS6\ShopBundle\Model\Cart\Exception\CartException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Zboží se nepodařilo vložit do košíku.'));
			}
		} else {
			// Form errors list in flash message is temporary solution.
			// We need to determine couse of error when adding product to cart.
			$flashMessageBag = $this->get('ss6.shop.component.flash_message.bag.front');
			$formErrors = $this->errorService->getAllErrorsAsArray($form, $flashMessageBag);
			$this->getFlashMessageSender()->addErrorFlashTwig(
				t('Zadejte prosím platné množství, které chcete vložit do košíku.<br/> {{ errors|raw }}'),
				[
					'errors' => implode('<br/>', $formErrors),
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

				return $this->render('@SS6Shop/Front/Inline/Cart/afterAddWindow.html.twig', [
					'accessoryDetails' => $accessoryDetails,
					'ACCESSORIES_ON_BUY' => ModuleList::ACCESSORIES_ON_BUY,
				]);
			} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Zvolené zboží již není v nabídce nebo neexistuje.'));
			} catch (\SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Zadejte prosím platné množství, které chcete vložit do košíku.'));
			} catch (\SS6\ShopBundle\Model\Cart\Exception\CartException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Zboží se nepodařilo vložit do košíku.'));
			}
		} else {
			// Form errors list in flash message is temporary solution.
			// We need to determine couse of error when adding product to cart.
			$flashMessageBag = $this->get('ss6.shop.component.flash_message.bag.front');
			$formErrors = $this->errorService->getAllErrorsAsArray($form, $flashMessageBag);
			$this->getFlashMessageSender()->addErrorFlashTwig(
				t('Zadejte prosím platné množství, které chcete vložit do košíku.<br/> {{ errors|raw }}'),
				[
					'errors' => implode('<br/>', $formErrors),
				]
			);
		}

		return $this->forward('SS6ShopBundle:Front/FlashMessage:index');
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\AddProductResult $addProductResult
	 */
	private function sendAddProductResultFlashMessage(
		AddProductResult $addProductResult
	) {
		if ($addProductResult->getIsNew()) {
			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Do košíku bylo vloženo zboží <strong>{{ name }}</strong> ({{ quantity|formatNumber }} {{ unitName }})'),
				[
					'name' => $addProductResult->getCartItem()->getName(),
					'quantity' => $addProductResult->getAddedQuantity(),
					'unitName' => $addProductResult->getCartItem()->getProduct()->getUnit()->getName(),
				]
			);
		} else {
			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Do košíku bylo vloženo zboží <strong>{{ name }}</strong> (celkem již {{ quantity|formatNumber }} {{ unitName }})'),
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
					t('Z košíku bylo odstraněno zboží {{ name }}'),
					['name' => $productName]
				);
			} catch (\SS6\ShopBundle\Model\Cart\Exception\InvalidCartItemException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Nepodařilo se odstranit položku z košíku. Nejspíš je již odstraněno'));
			}
		} else {
			$this->getFlashMessageSender()->addErrorFlash(
				t('Nepodařilo se odstranit položku z košíku.
					Zřejmě vypršela platnost odkazu pro jeho smazání, proto to vyzkoušejte ještě jednou.'
				)
			);
		}

		return $this->redirectToRoute('front_cart');
	}

}

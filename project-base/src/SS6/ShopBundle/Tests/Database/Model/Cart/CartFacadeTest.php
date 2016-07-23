<?php

namespace SS6\ShopBundle\Tests\Database\Model\Cart;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\CartFacade;
use SS6\ShopBundle\Model\Cart\CartFactory;
use SS6\ShopBundle\Model\Cart\CartService;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Cart\Item\CartItemRepository;
use SS6\ShopBundle\Model\Cart\Watcher\CartWatcherFacade;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class CartFacadeTest extends DatabaseTestCase {

	public function testAddProductToCart() {
		$cartService = $this->getContainer()->get(CartService::class);
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		$customerIdentifier = new CustomerIdentifier('secreetSessionHash');
		$cartItemRepository = $this->getContainer()->get(CartItemRepository::class);
		$cartWatcherFacade = $this->getContainer()->get(CartWatcherFacade::class);
		$domain = $this->getContainer()->get(Domain::class);
		$currentCustomer = $this->getContainer()->get(CurrentCustomer::class);

		$product1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		$productId = $product1->getId();
		$quantity = 10;

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$cartFacade = new CartFacade(
			$this->getEntityManager(),
			$cartService,
			$cart,
			$productRepository,
			$customerIdentifier,
			$domain,
			$currentCustomer
		);
		$cartFacade->addProductToCart($productId, $quantity);

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$cartItems = $cart->getItems();
		$product1 = array_pop($cartItems)->getProduct();
		$this->assertSame($productId, $product1->getId(), 'Add correct product');

		$customerIdentifier = new CustomerIdentifier('anotherSecreetSessionHash');
		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$this->assertSame(0, $cart->getItemsCount(), 'Add only in their own cart');
	}

	public function testAddUnsellableProductToCart() {
		$cartService = $this->getContainer()->get(CartService::class);
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		$customerIdentifier = new CustomerIdentifier('secreetSessionHash');
		$cartItemRepository = $this->getContainer()->get(CartItemRepository::class);
		$cartWatcherFacade = $this->getContainer()->get(CartWatcherFacade::class);
		$domain = $this->getContainer()->get(Domain::class);
		$currentCustomer = $this->getContainer()->get(CurrentCustomer::class);

		$product = $this->getReference('product_6');
		$productId = $product->getId();
		$quantity = 1;

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$cartFacade = new CartFacade(
			$this->getEntityManager(),
			$cartService,
			$cart,
			$productRepository,
			$customerIdentifier,
			$domain,
			$currentCustomer
		);

		// @codingStandardsIgnoreStart
		try {
			$cartFacade->addProductToCart($productId, $quantity);
		} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $e) {

		}
		// @codingStandardsIgnoreEnd

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$cartItems = $cart->getItems();

		$this->assertEmpty($cartItems, 'Product add not suppressed');
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function testChangeQuantities() {
		$cartService = $this->getContainer()->get(CartService::class);
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		$customerIdentifier = new CustomerIdentifier('secreetSessionHash');
		$cartItemRepository = $this->getContainer()->get(CartItemRepository::class);
		$cartWatcherFacade = $this->getContainer()->get(CartWatcherFacade::class);
		$domain = $this->getContainer()->get(Domain::class);
		$currentCustomer = $this->getContainer()->get(CurrentCustomer::class);

		$product1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		$product2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '3');

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$cartFacade = new CartFacade(
			$this->getEntityManager(),
			$cartService,
			$cart,
			$productRepository,
			$customerIdentifier,
			$domain,
			$currentCustomer
		);
		$cartItem1 = $cartFacade->addProductToCart($product1->getId(), 1)->getCartItem();
		$cartItem2 = $cartFacade->addProductToCart($product2->getId(), 2)->getCartItem();

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$cartFacade = new CartFacade(
			$this->getEntityManager(),
			$cartService,
			$cart,
			$productRepository,
			$customerIdentifier,
			$domain,
			$currentCustomer
		);
		$cartFacade->changeQuantities([
			$cartItem1->getId() => 5,
			$cartItem2->getId() => 9,
		]);

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		foreach ($cart->getItems() as $cartItem) {
			if ($cartItem->getId() === $cartItem1->getId()) {
				$this->assertSame(5, $cartItem->getQuantity(), 'Correct change quantity product');
			} elseif ($cartItem->getId() === $cartItem2->getId()) {
				$this->assertSame(9, $cartItem->getQuantity(), 'Correct change quantity product');
			} else {
				$this->fail('Unexpected product in cart');
			}
		}
	}

	public function testDeleteCartItemNonexistItem() {
		$em = $this->getEntityManager();

		$cartService = $this->getContainer()->get(CartService::class);
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		$customerIdentifier = new CustomerIdentifier('randomString');
		$domain = $this->getContainer()->get(Domain::class);
		$currentCustomer = $this->getContainer()->get(CurrentCustomer::class);

		$product1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		$cartItem = new CartItem($customerIdentifier, $product1, 1, '0.0');
		$em->persist($cartItem);
		$em->flush();
		$cartItems = [$cartItem];
		$cart = new Cart($cartItems);

		$cartFacade = new CartFacade(
			$this->getEntityManager(),
			$cartService,
			$cart,
			$productRepository,
			$customerIdentifier,
			$domain,
			$currentCustomer
		);
		$this->setExpectedException('\SS6\ShopBundle\Model\Cart\Exception\InvalidCartItemException');
		$cartFacade->deleteCartItem($cartItem->getId() + 1);
	}

	public function testDeleteCartItem() {
		$em = $this->getEntityManager();

		// Set currentLocale in TranslatableListener as it done in real request
		// because CartWatcherFacade works with entity translations.
		$translatableListener = $this->getContainer()->get(\SS6\ShopBundle\Model\Localization\TranslatableListener::class);
		/* @var $translatableListener \SS6\ShopBundle\Model\Localization\TranslatableListener */
		$translatableListener->setCurrentLocale('cs');

		$cartService = $this->getContainer()->get(CartService::class);
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItemRepository = $this->getContainer()->get(CartItemRepository::class);
		$cartWatcherFacade = $this->getContainer()->get(CartWatcherFacade::class);
		$domain = $this->getContainer()->get(Domain::class);
		$currentCustomer = $this->getContainer()->get(CurrentCustomer::class);

		$product1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		$product2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '3');
		$cartItem1 = new CartItem($customerIdentifier, $product1, 1, '0.0');
		$cartItem2 = new CartItem($customerIdentifier, $product2, 1, '0.0');
		$em->persist($cartItem1);
		$em->persist($cartItem2);
		$em->flush();
		$cartItems = [$cartItem1, $cartItem2];
		$cart = new Cart($cartItems);

		$cartFacade = new CartFacade(
			$this->getEntityManager(),
			$cartService,
			$cart,
			$productRepository,
			$customerIdentifier,
			$domain,
			$currentCustomer
		);
		$cartFacade->deleteCartItem($cartItem1->getId());

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$this->assertSame(1, $cart->getItemsCount());
		$cartItems = $cart->getItems();
		$cartItem = array_pop($cartItems);
		$this->assertSame($cartItem2->getId(), $cartItem->getId());
	}

}

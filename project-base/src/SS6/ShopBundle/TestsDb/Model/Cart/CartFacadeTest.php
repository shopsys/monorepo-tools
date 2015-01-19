<?php

namespace SS6\ShopBundle\TestsDb\Model\Cart;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\CartFacade;
use SS6\ShopBundle\Model\Cart\CartFactory;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\ProductEditData;

class CartFacadeTest extends DatabaseTestCase {

	public function testAddProductToCart() {
		$em = $this->getEntityManager();
		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		$productRepository = $this->getContainer()->get('ss6.shop.product.product_repository');
		$customerIdentifier = new CustomerIdentifier('secreetSessionHash');
		$cartItemRepository = $this->getContainer()->get('ss6.shop.cart.item.cart_item_repository');
		$cartWatcherFacade = $this->getContainer()->get('ss6.shop.cart.cart_watcher_facade');
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		$domain = $this->getContainer()->get('ss6.shop.domain');

		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);
		$productEditData = new ProductEditData();
		$productEditData->productData->name = ['cs' => 'productName'];
		$productEditData->productData->vat = $vat;
		$productEditData->productData->price = 1;
		$productEditData->productData->hiddenOnDomains = array(2);
		$product = $productEditFacade->create($productEditData);
		$em->flush();
		$productId = $product->getId();
		$em->clear();
		$quantity = 10;
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$cartFacade = new CartFacade($this->getEntityManager(), $cartService, $cart, $productRepository, $customerIdentifier, $domain);
		$cartFacade->addProductToCart($productId, $quantity);

		$em->clear();
		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$cartItems = $cart->getItems();
		$product = array_pop($cartItems)->getProduct();
		$this->assertEquals($productId, $product->getId(), 'Add correct product');

		$customerIdentifier = new CustomerIdentifier('anotherSecreetSessionHash');
		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$this->assertEquals(0, $cart->getItemsCount(), 'Add only in their own cart');
	}

	public function testChangeQuantities() {
		$em = $this->getEntityManager();
		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		$productRepository = $this->getContainer()->get('ss6.shop.product.product_repository');
		$customerIdentifier = new CustomerIdentifier('secreetSessionHash');
		$cartItemRepository = $this->getContainer()->get('ss6.shop.cart.item.cart_item_repository');
		$cartWatcherFacade = $this->getContainer()->get('ss6.shop.cart.cart_watcher_facade');
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		$domain = $this->getContainer()->get('ss6.shop.domain');

		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);

		$productEditData = new ProductEditData();
		$productEditData->productData->name = ['cs' => 'productName'];
		$productEditData->productData->vat = $vat;
		$productEditData->productData->price = 1;
		$productEditData->productData->hiddenOnDomains = array(2);
		$product1 = $productEditFacade->create($productEditData);
		$product2 = $productEditFacade->create($productEditData);
		$em->flush();
		$em->clear();
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$cartFacade = new CartFacade($this->getEntityManager(), $cartService, $cart, $productRepository, $customerIdentifier, $domain);
		$cartItem1 = $cartFacade->addProductToCart($product1->getId(), 1)->getCartItem();
		$cartItem2 = $cartFacade->addProductToCart($product2->getId(), 2)->getCartItem();
		$em->flush();
		$em->clear();

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$cartFacade = new CartFacade($this->getEntityManager(), $cartService, $cart, $productRepository, $customerIdentifier, $domain);
		$cartFacade->changeQuantities(array(
			$cartItem1->getId() => 5,
			$cartItem2->getId() => 9,
		));
		$em->flush();

		$em->clear();
		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		foreach ($cart->getItems() as $cartItem) {
			if ($cartItem->getId() === $cartItem1->getId()) {
				$this->assertEquals(5, $cartItem->getQuantity(), 'Correct change quantity product');
			} elseif ($cartItem->getId() === $cartItem2->getId()) {
				$this->assertEquals(9, $cartItem->getQuantity(), 'Correct change quantity product');
			} else {
				$this->fail('Unexpected product in cart');
			}
		}
	}

	public function testDeleteCartItemNonexistItem() {
		$em = $this->getEntityManager();

		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		$productRepository = $this->getContainer()->get('ss6.shop.product.product_repository');
		$customerIdentifier = new CustomerIdentifier('randomString');
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		$domain = $this->getContainer()->get('ss6.shop.domain');

		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);

		$productEditData = new ProductEditData();
		$productEditData->productData->name = ['cs' => 'productName'];
		$productEditData->productData->vat = $vat;
		$productEditData->productData->price = 1;
		$productEditData->productData->hiddenOnDomains = array(2);
		$product = $productEditFacade->create($productEditData);
		$cartItem = new CartItem($customerIdentifier, $product, 1, '0.0');
		$em->persist($cartItem);
		$cartItems = array($cartItem);
		$cart = new Cart($cartItems);
		$em->flush();

		$cartFacade = new CartFacade($this->getEntityManager(), $cartService, $cart, $productRepository, $customerIdentifier, $domain);
		$this->setExpectedException('\SS6\ShopBundle\Model\Cart\Exception\InvalidCartItemException');
		$cartFacade->deleteCartItem($cartItem->getId() + 1);
	}

	public function testDeleteCartItem() {
		$em = $this->getEntityManager();

		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		$productRepository = $this->getContainer()->get('ss6.shop.product.product_repository');
		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItemRepository = $this->getContainer()->get('ss6.shop.cart.item.cart_item_repository');
		$cartWatcherFacade = $this->getContainer()->get('ss6.shop.cart.cart_watcher_facade');
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		$domain = $this->getContainer()->get('ss6.shop.domain');

		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);

		$productEditData = new ProductEditData();
		$productEditData->productData->name = ['cs' => 'productName'];
		$productEditData->productData->vat = $vat;
		$productEditData->productData->price = 1;
		$productEditData->productData->hiddenOnDomains = array(2);
		$product1 = $productEditFacade->create($productEditData);
		$product2 = $productEditFacade->create($productEditData);
		$cartItem1 = new CartItem($customerIdentifier, $product1, 1, '0.0');
		$cartItem2 = new CartItem($customerIdentifier, $product2, 1, '0.0');
		$em->persist($cartItem1);
		$em->persist($cartItem2);
		$cartItems = array($cartItem1, $cartItem2);
		$cart = new Cart($cartItems);
		$em->flush();

		$cartFacade = new CartFacade($this->getEntityManager(), $cartService, $cart, $productRepository, $customerIdentifier, $domain);
		$cartFacade->deleteCartItem($cartItem1->getId());

		$em->clear();

		// new products do not have calculated visibility
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);
		$cart = $cartFactory->get($customerIdentifier);
		$this->assertEquals(1, $cart->getItemsCount());
		$cartItems = $cart->getItems();
		$cartItem = array_pop($cartItems);
		$this->assertEquals($cartItem2->getId(), $cartItem->getId());
	}

}
